<x-layouts.app title="Recycle Bin">
    <x-slot:header>
        Recycle Bin
    </x-slot:header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-slate-900">Deleted Parties</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Recover accidentally deleted records or permanently erase them.
                </p>
            </div>
            <a href="{{ route('parties.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition">
                &larr; Back to Parties
            </a>
        </div>

        <!-- Trashed Parties Table -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="theme-table-head-row border-b border-slate-100 bg-slate-50/70 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-6">Party Name</th>
                            <th class="py-3 px-6">Mobile Number</th>
                            <th class="py-3 px-6">Deleted At</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($trashedParties as $party)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-6 font-semibold text-slate-900">
                                    {{ $party->party_name }}
                                </td>
                                <td class="py-3.5 px-6 text-slate-500 font-mono text-xs">
                                    {{ $party->mobile_number ?: '—' }}
                                </td>
                                <td class="py-3.5 px-6 text-slate-500 text-xs">
                                    {{ $party->deleted_at->diffForHumans() }}
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Restore Button -->
                                        <form method="POST" action="{{ route('recycle_bin.restore', $party->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold border border-emerald-200 transition">
                                                Restore
                                            </button>
                                        </form>

                                        <!-- Permanent Delete Button -->
                                        <form method="POST" action="{{ route('recycle_bin.forceDelete', $party->id) }}" class="inline" onsubmit="return confirm('Permanently erase this record? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-semibold border border-rose-200 transition">
                                                Purge
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-400 text-sm">
                                    Recycle bin is empty. No deleted parties found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $trashedParties->links() }}
            </div>
        </div>

    </div>
</x-layouts.app>
