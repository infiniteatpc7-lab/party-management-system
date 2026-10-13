<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartyRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Models\Party;
use App\Services\CoordinateExtractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartyController extends Controller
{
    /**
     * Display a listing of parties with search, multi-criteria filters, and distance sorting.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $type = $request->input('type', 'all');
        $priority = $request->input('priority', 'all');
        $lead = $request->input('lead', 'all');
        $radius = $request->input('radius', 'all');
        $sort = $request->input('sort', 'newest');

        $userLat = $request->has('lat') && is_numeric($request->input('lat'))
            ? (float) $request->input('lat')
            : null;
        $userLng = $request->has('lng') && is_numeric($request->input('lng'))
            ? (float) $request->input('lng')
            : null;

        $hasLocation = ($userLat !== null && $userLng !== null);

        $query = Party::query();

        // Include distance calculation if location is available
        if ($hasLocation) {
            $query->withDistance($userLat, $userLng);

            // Radius filter
            if ($radius !== 'all' && is_numeric($radius)) {
                $query->withinRadius($userLat, $userLng, (float) $radius);
            }
        }

        // Search filter
        $query->search($search);

        // Type filter (new / old)
        $query->type($type);

        // Priority filter (priority / non_priority)
        $query->priority($priority);

        // Lead filter (lead / non_lead)
        $query->lead($lead);

        // Sorting
        if ($hasLocation && in_array($sort, ['nearest', 'farthest'], true)) {
            $direction = $sort === 'nearest' ? 'asc' : 'desc';
            $query->orderBy('distance', $direction);
        } elseif ($sort === 'name_asc') {
            $query->orderBy('party_name', 'asc');
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $parties = $query->paginate(15)->withQueryString();

        return view('parties.index', [
            'parties' => $parties,
            'search' => $search,
            'type' => $type,
            'priority' => $priority,
            'lead' => $lead,
            'radius' => $radius,
            'sort' => $sort,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'hasLocation' => $hasLocation,
        ]);
    }

    /**
     * Display the full interactive map view with split-screen sidebar and marker clusters.
     */
    public function map(Request $request): View
    {
        $search = $request->input('search');
        $type = $request->input('type', 'all');
        $priority = $request->input('priority', 'all');
        $lead = $request->input('lead', 'all');
        $radius = $request->input('radius', 'all');

        $userLat = $request->has('lat') && is_numeric($request->input('lat'))
            ? (float) $request->input('lat')
            : null;
        $userLng = $request->has('lng') && is_numeric($request->input('lng'))
            ? (float) $request->input('lng')
            : null;

        $hasLocation = ($userLat !== null && $userLng !== null);

        $query = Party::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($hasLocation) {
            $query->withDistance($userLat, $userLng);

            if ($radius !== 'all' && is_numeric($radius)) {
                $query->withinRadius($userLat, $userLng, (float) $radius);
            }

            $query->orderBy('distance', 'asc');
        } else {
            $query->latest();
        }

        $query->search($search);
        $query->type($type);
        $query->priority($priority);
        $query->lead($lead);

        $parties = $query->get();

        $mapPins = $parties->map(fn (Party $p) => [
            'id' => $p->id,
            'name' => $p->party_name,
            'mobile' => $p->mobile_number,
            'lat' => $p->latitude,
            'lng' => $p->longitude,
            'type' => $p->party_type,
            'priority' => $p->priority_status,
            'is_lead' => (bool) $p->is_lead,
            'distance' => isset($p->distance) ? $p->formatted_distance : null,
            'distance_km' => isset($p->distance) ? (float) $p->distance : null,
            'nav_url' => $p->google_navigation_url,
            'edit_url' => route('parties.edit', $p->id),
        ]);

        return view('parties.map', [
            'parties' => $parties,
            'mapPins' => $mapPins,
            'search' => $search,
            'type' => $type,
            'priority' => $priority,
            'lead' => $lead,
            'radius' => $radius,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'hasLocation' => $hasLocation,
        ]);
    }

    /**
     * Show the form for creating a new party.
     */
    public function create(): View
    {
        return view('parties.create');
    }

    /**
     * Store a newly created party in storage.
     */
    public function store(StorePartyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Check for duplicate party name and mobile
        $duplicate = Party::where('party_name', $validated['party_name'])
            ->when(! empty($validated['mobile_number']), function ($q) use ($validated) {
                $q->where('mobile_number', $validated['mobile_number']);
            })
            ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'party_name' => 'A party with this name and mobile number already exists.',
            ]);
        }

        $party = Party::create($validated);

        return redirect()->route('parties.index')
            ->with('toast_success', "Party '{$party->party_name}' created successfully!");
    }

    /**
     * Show the form for editing the specified party.
     */
    public function edit(Party $party): View
    {
        return view('parties.edit', [
            'party' => $party,
        ]);
    }

    /**
     * Update the specified party in storage.
     */
    public function update(UpdatePartyRequest $request, Party $party): RedirectResponse
    {
        $validated = $request->validated();

        // Check for duplicate
        $duplicate = Party::where('party_name', $validated['party_name'])
            ->where('id', '!=', $party->id)
            ->when(! empty($validated['mobile_number']), function ($q) use ($validated) {
                $q->where('mobile_number', $validated['mobile_number']);
            })
            ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'party_name' => 'Another party with this name and mobile number already exists.',
            ]);
        }

        $party->update($validated);

        return redirect()->route('parties.index')
            ->with('toast_success', "Party '{$party->party_name}' updated successfully!");
    }

    /**
     * Remove the specified party from storage (Soft Delete).
     */
    public function destroy(Party $party): RedirectResponse
    {
        $name = $party->party_name;
        $party->delete();

        return redirect()->route('parties.index')
            ->with('toast_success', "Party '{$name}' deleted successfully.");
    }

    /**
     * AJAX endpoint to extract coordinates from Google Maps URL or raw text.
     */
    public function extractCoordinates(Request $request): JsonResponse
    {
        $url = $request->input('url');
        if (empty($url)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid Google Maps link or coordinates.',
            ], 422);
        }

        $coords = CoordinateExtractor::extract($url);

        if (! $coords) {
            return response()->json([
                'success' => false,
                'message' => 'Could not extract coordinates from the provided link. Please ensure the link is a valid Google Maps URL.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'latitude' => $coords['latitude'],
            'longitude' => $coords['longitude'],
        ]);
    }
}
