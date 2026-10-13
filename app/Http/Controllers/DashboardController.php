<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with real-time KPI metrics and location previews.
     */
    public function index(Request $request): View
    {
        $userLat = $request->has('lat') && is_numeric($request->input('lat'))
            ? (float) $request->input('lat')
            : null;
        $userLng = $request->has('lng') && is_numeric($request->input('lng'))
            ? (float) $request->input('lng')
            : null;

        $hasLocation = ($userLat !== null && $userLng !== null);

        // Core KPIs straight from database
        $totalParties = Party::count();
        $priorityParties = Party::where('priority_status', 'priority')->count();
        $nonPriorityParties = Party::where('priority_status', 'non_priority')->count();
        $newParties = Party::where('party_type', 'new')->count();
        $oldParties = Party::where('party_type', 'old')->count();

        // Nearby count calculation
        if ($hasLocation) {
            $nearbyPartiesCount = Party::withinRadius($userLat, $userLng, 10)->count();
            $nearbyParties = Party::withDistance($userLat, $userLng)
                ->orderBy('distance')
                ->take(6)
                ->get();
        } else {
            $nearbyPartiesCount = Party::whereNotNull('latitude')->whereNotNull('longitude')->count();
            $nearbyParties = Party::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->latest()
                ->take(6)
                ->get();
        }

        // Map pins for the preview card
        $mapPartiesQuery = Party::whereNotNull('latitude')->whereNotNull('longitude');
        if ($hasLocation) {
            $mapParties = Party::withDistance($userLat, $userLng)
                ->orderBy('distance')
                ->take(50)
                ->get();
        } else {
            $mapParties = $mapPartiesQuery->latest()->take(50)->get();
        }

        $mapPins = $mapParties->map(fn (Party $p) => [
            'id' => $p->id,
            'name' => $p->party_name,
            'mobile' => $p->mobile_number,
            'lat' => $p->latitude,
            'lng' => $p->longitude,
            'type' => $p->party_type,
            'priority' => $p->priority_status,
            'distance' => isset($p->distance) ? $p->formatted_distance : null,
            'nav_url' => $p->google_navigation_url,
        ]);

        return view('dashboard', [
            'totalParties' => $totalParties,
            'nearbyPartiesCount' => $nearbyPartiesCount,
            'priorityParties' => $priorityParties,
            'nonPriorityParties' => $nonPriorityParties,
            'newParties' => $newParties,
            'oldParties' => $oldParties,
            'nearbyParties' => $nearbyParties,
            'mapPins' => $mapPins,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'hasLocation' => $hasLocation,
        ]);
    }
}
