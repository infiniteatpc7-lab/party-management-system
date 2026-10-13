<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RecycleBinController extends Controller
{
    /**
     * Display deleted parties.
     */
    public function index(): View
    {
        $trashedParties = Party::onlyTrashed()->latest('deleted_at')->paginate(15);

        return view('recycle_bin.index', [
            'trashedParties' => $trashedParties,
        ]);
    }

    /**
     * Restore a soft-deleted party.
     */
    public function restore(int $id): RedirectResponse
    {
        $party = Party::onlyTrashed()->findOrFail($id);
        $party->restore();

        return redirect()->route('recycle_bin.index')
            ->with('toast_success', "Party '{$party->party_name}' has been restored.");
    }

    /**
     * Permanently delete a party.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $party = Party::onlyTrashed()->findOrFail($id);
        $name = $party->party_name;
        $party->forceDelete();

        return redirect()->route('recycle_bin.index')
            ->with('toast_success', "Party '{$name}' permanently deleted.");
    }
}
