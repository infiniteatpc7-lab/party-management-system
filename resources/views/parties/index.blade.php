<x-layouts.app title="All Parties">
    <x-slot:header>
        Party Directory
    </x-slot:header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Top Header & Primary Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-slate-900">Manage Parties</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Search, filter, edit, and navigate to client parties.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('import.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200/80 shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import Excel</span>
                </a>
                <a href="{{ route('parties.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs shadow-indigo-600/20 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Party</span>
                </a>
            </div>
        </div>

        <!-- Compact Search & Filter Bar (Matching Reference) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-3 sm:p-4 shadow-xs">
            <!-- Hidden inputs preserving filter state and coordinates -->
            <form id="filter-form" method="GET" action="{{ route('parties.index') }}" onsubmit="event.preventDefault();">
                <input type="hidden" name="lat" id="filter-lat" value="{{ $userLat }}">
                <input type="hidden" name="lng" id="filter-lng" value="{{ $userLng }}">
                <input type="hidden" name="type" id="filter-type-input" value="{{ $type }}">
                <input type="hidden" name="priority" id="filter-priority-input" value="{{ $priority }}">
                <input type="hidden" name="lead" id="filter-lead-input" value="{{ $lead }}">
                <input type="hidden" name="radius" id="filter-radius-input" value="{{ $radius }}">
                <input type="hidden" name="sort" id="filter-sort-input" value="{{ $sort }}">

                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <input type="text" name="search" id="live-search-input" value="{{ $search }}" placeholder="Search by name or mobile..."
                        autocomplete="off"
                        class="w-full pl-10 pr-20 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-hidden focus:ring-2 focus:ring-indigo-600/20 transition">

                    <!-- Spinner, Clear button and Filter Modal Trigger Button on the right -->
                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center gap-1.5">
                        <!-- Live search loading spinner -->
                        <div id="search-spinner" class="hidden pr-1 flex items-center pointer-events-none">
                            <svg class="animate-spin w-4 h-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <!-- Clear search button -->
                        <button type="button" id="search-clear-btn" title="Clear search" class="{{ empty($search) ? 'hidden' : '' }} p-1 text-slate-400 hover:text-slate-600 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <!-- Filter Modal Toggle Button (Image 3 style) -->
                        <button type="button" id="open-filter-modal-btn" onclick="openFilterModal()" title="Open Filters"
                            class="filter-trigger-btn relative p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer">
                            <!-- Sliders icon matching image 3 -->
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <!-- Active filter indicator badge dot -->
                            <span id="filter-active-dot" class="{{ ($type !== 'all' || $priority !== 'all' || $lead !== 'all' || $radius !== 'all' || $sort !== 'newest') ? '' : 'hidden' }} absolute top-1 right-1 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        </button>
                    </div>
                </div>

                <!-- Sub-bar: Matching count & Active Filter Badges -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-2.5 text-xs">
                    <div class="text-slate-500">
                        Showing <span id="matching-count" class="font-bold text-slate-800">{{ $parties->total() }}</span> matching parties
                    </div>
                    <div id="active-filter-chips" class="flex flex-wrap items-center gap-1.5">
                        @if($type !== 'all')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700">
                                Type: {{ ucfirst($type) }}
                                <button type="button" onclick="clearSpecificFilter('type')" class="hover:text-rose-600 cursor-pointer">×</button>
                            </span>
                        @endif
                        @if($priority !== 'all')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700">
                                {{ $priority === 'priority' ? 'Priority' : 'Non-Priority' }}
                                <button type="button" onclick="clearSpecificFilter('priority')" class="hover:text-rose-600 cursor-pointer">×</button>
                            </span>
                        @endif
                        @if($lead !== 'all')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-200/80 theme-lead-badge">
                                {{ $lead === 'lead' ? 'Lead' : 'Non-Lead' }}
                                <button type="button" onclick="clearSpecificFilter('lead')" class="hover:text-rose-600 cursor-pointer">×</button>
                            </span>
                        @endif
                        @if($radius !== 'all')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700">
                                {{ $radius }} km
                                <button type="button" onclick="clearSpecificFilter('radius')" class="hover:text-rose-600 cursor-pointer">×</button>
                            </span>
                        @endif
                        @if($sort !== 'newest')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700">
                                {{ $sort === 'nearest' ? 'Nearest First' : ($sort === 'farthest' ? 'Farthest First' : ($sort === 'name_asc' ? 'Name (A-Z)' : 'Newest')) }}
                                <button type="button" onclick="clearSpecificFilter('sort')" class="hover:text-rose-600 cursor-pointer">×</button>
                            </span>
                        @endif
                        @if($type !== 'all' || $priority !== 'all' || $lead !== 'all' || $radius !== 'all' || $sort !== 'newest')
                            <button type="button" onclick="resetModalFilters()" class="text-[11px] text-rose-600 hover:underline font-semibold ml-1 cursor-pointer">
                                Clear All
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Party Records: Desktop Table & Mobile Cards -->
        <div id="parties-data-container" class="bg-white rounded-2xl shadow-xs overflow-hidden">

            <!-- Desktop View Table (hidden on small screens) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="theme-table-head-row bg-slate-50/70 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-6">Party Name</th>
                            <th class="py-3 px-6">Mobile</th>
                            <th class="py-3 px-6">Type</th>
                            <th class="py-3 px-6">Priority</th>
                            <th class="py-3 px-6">Distance</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($parties as $party)
                            <tr class="hover:bg-slate-50/60 transition group">
                                <td class="py-3.5 px-6 font-semibold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $party->party_name }}</span>
                                        @if($party->is_lead)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200/80 theme-lead-badge" title="Client Lead">
                                                Lead
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-slate-600 font-mono text-xs">
                                    {{ $party->mobile_number ?: '—' }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $party->isNew() ? 'bg-blue-50 text-blue-700 border border-blue-200/70' : 'bg-amber-50 text-amber-700 border border-amber-200/70' }}">
                                        {{ ucfirst($party->party_type) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6">
                                    @if($party->isPriority())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Priority
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Non-Priority
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 font-mono text-xs party-distance-cell" data-party-lat="{{ $party->latitude }}" data-party-lng="{{ $party->longitude }}">
                                    @if(isset($party->distance))
                                        <span class="inline-flex items-center gap-1 font-semibold theme-distance-pill px-2 py-0.5 rounded-md text-xs">
                                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $party->formatted_distance }}
                                        </span>
                                    @elseif(!$hasLocation)
                                        <span class="party-distance-val text-slate-400 italic text-xs">GPS Idle</span>
                                    @else
                                        <span class="text-slate-400 text-xs">No coords</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if($party->google_navigation_url)
                                            <a href="{{ $party->google_navigation_url }}" target="_blank" rel="noopener noreferrer" title="Navigate in Google Maps"
                                                class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('parties.edit', $party->id) }}" title="Edit Party"
                                            class="p-1.5 text-slate-400 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        <button type="button" onclick="confirmDelete({{ $party->id }}, '{{ addslashes($party->party_name) }}')" title="Delete Party"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                    No parties matching the selected search and filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Clean Card List (visible on < md screens) -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($parties as $party)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $party->party_name }}</h3>
                                    @if($party->is_lead)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200/80 theme-lead-badge">
                                            Lead
                                        </span>
                                    @endif
                                </div>
                                @if($party->mobile_number)
                                    <p class="text-xs text-slate-500 font-mono mt-0.5">
                                        <a href="tel:{{ $party->mobile_number }}" class="hover:text-indigo-600">{{ $party->mobile_number }}</a>
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($party->isPriority())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Priority
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Normal
                                    </span>
                                @endif

                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $party->isNew() ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst($party->party_type) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div class="party-distance-cell-mobile" data-party-lat="{{ $party->latitude }}" data-party-lng="{{ $party->longitude }}">
                                @if(isset($party->distance))
                                    <span class="inline-flex items-center gap-1 font-semibold theme-distance-pill px-2 py-0.5 rounded text-xs">
                                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $party->formatted_distance }} away
                                    </span>
                                @elseif(!$hasLocation)
                                    <span class="party-distance-val text-xs text-slate-400 italic">GPS idle</span>
                                @else
                                    <span class="text-xs text-slate-400">No coordinates</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if($party->google_navigation_url)
                                    <a href="{{ $party->google_navigation_url }}" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-semibold hover:bg-indigo-100 transition">
                                        Navigate
                                    </a>
                                @endif
                                <a href="{{ route('parties.edit', $party->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium hover:bg-slate-200 transition">
                                    Edit
                                </a>
                                <button type="button" onclick="confirmDelete({{ $party->id }}, '{{ addslashes($party->party_name) }}')" class="p-1 text-rose-500 hover:text-rose-700">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        No parties found matching selected criteria.
                    </div>
                @endforelse
            </div>

            <!-- Pagination Bar -->
            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $parties->links() }}
            </div>
        </div>

    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" onclick="closeDeleteModal()"></div>
        <div class="relative bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-200">
            <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Delete Party</h3>
            <p class="text-xs text-slate-500 mt-1">Are you sure you want to delete <span id="delete-party-name" class="font-semibold text-slate-800"></span>? You can restore it later from the Recycle Bin.</p>

            <form id="delete-form" method="POST" action="" class="mt-5 flex items-center justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition">Delete</button>
            </form>
        </div>
    </div>

    <!-- Filter Parties Modal (Matching Reference Image 4) -->
    <div id="filter-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <!-- Backdrop with smooth fade -->
        <div id="filter-modal-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeFilterModal()"></div>

        <!-- Modal Card with smooth scale & fade -->
        <div id="filter-modal-card" class="relative bg-white rounded-3xl max-w-sm sm:max-w-md w-full p-6 shadow-2xl border border-slate-200 transition-all duration-300 transform scale-95 opacity-0">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Filter Parties</h3>
                <button type="button" onclick="closeFilterModal()" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body: Filter Option Groups -->
            <div class="py-4 space-y-4 text-xs sm:text-sm">
                <!-- Group 1: Party Type -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Party Type</label>
                    <div class="grid grid-cols-3 gap-2" data-filter-group="type">
                        <button type="button" data-value="all" class="filter-pill-btn {{ $type === 'all' ? 'active' : '' }}">All</button>
                        <button type="button" data-value="new" class="filter-pill-btn {{ $type === 'new' ? 'active' : '' }}">New</button>
                        <button type="button" data-value="old" class="filter-pill-btn {{ $type === 'old' ? 'active' : '' }}">Old</button>
                    </div>
                </div>

                <!-- Group 2: Priority -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Priority</label>
                    <div class="grid grid-cols-3 gap-2" data-filter-group="priority">
                        <button type="button" data-value="all" class="filter-pill-btn {{ $priority === 'all' ? 'active' : '' }}">All</button>
                        <button type="button" data-value="priority" class="filter-pill-btn {{ $priority === 'priority' ? 'active' : '' }}">Priority</button>
                        <button type="button" data-value="non_priority" class="filter-pill-btn {{ $priority === 'non_priority' ? 'active' : '' }}">Not Priority</button>
                    </div>
                </div>

                <!-- Group 3: Lead Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Lead Status</label>
                    <div class="grid grid-cols-3 gap-2" data-filter-group="lead">
                        <button type="button" data-value="all" class="filter-pill-btn {{ $lead === 'all' ? 'active' : '' }}">All</button>
                        <button type="button" data-value="lead" class="filter-pill-btn {{ $lead === 'lead' ? 'active' : '' }}">Lead</button>
                        <button type="button" data-value="non_lead" class="filter-pill-btn {{ $lead === 'non_lead' ? 'active' : '' }}">Not Lead</button>
                    </div>
                </div>

                <!-- Group 4: Distance (km) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Distance (km)</label>
                    <div class="grid grid-cols-4 gap-2" data-filter-group="radius">
                        <button type="button" data-value="all" class="filter-pill-btn {{ $radius === 'all' ? 'active' : '' }}">All</button>
                        <button type="button" data-value="1" class="filter-pill-btn {{ $radius === '1' ? 'active' : '' }}">1 km</button>
                        <button type="button" data-value="5" class="filter-pill-btn {{ $radius === '5' ? 'active' : '' }}">5 km</button>
                        <button type="button" data-value="10" class="filter-pill-btn {{ $radius === '10' ? 'active' : '' }}">10 km</button>
                    </div>
                </div>

                <!-- Group 4: Sort By -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Sort By</label>
                    <div class="grid grid-cols-2 gap-2" data-filter-group="sort">
                        <button type="button" data-value="nearest" class="filter-pill-btn {{ $sort === 'nearest' ? 'active' : '' }}">Nearest First</button>
                        <button type="button" data-value="newest" class="filter-pill-btn {{ $sort === 'newest' ? 'active' : '' }}">Newest First</button>
                        <button type="button" data-value="name_asc" class="filter-pill-btn {{ $sort === 'name_asc' ? 'active' : '' }}">Name (A-Z)</button>
                        <button type="button" data-value="farthest" class="filter-pill-btn {{ $sort === 'farthest' ? 'active' : '' }}">Farthest</button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer: Apply and Reset Buttons -->
            <div class="pt-2 space-y-3">
                <button type="button" onclick="applyModalFilters()" class="filter-modal-apply-btn w-full py-3 rounded-xl font-semibold text-sm transition cursor-pointer">
                    Apply Filter
                </button>
                <div class="text-center">
                    <button type="button" onclick="resetModalFilters()" class="filter-modal-reset-btn text-xs font-semibold transition cursor-pointer">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Modal & Filter State Helpers
        function openFilterModal() {
            const modal = document.getElementById('filter-modal');
            const backdrop = document.getElementById('filter-modal-backdrop');
            const card = document.getElementById('filter-modal-card');
            if (!modal || !backdrop || !card) return;

            modal.classList.remove('hidden');
            // Trigger smooth transition
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                card.classList.remove('opacity-0', 'scale-95');
                card.classList.add('opacity-100', 'scale-100');
            });
        }

        function closeFilterModal() {
            const modal = document.getElementById('filter-modal');
            const backdrop = document.getElementById('filter-modal-backdrop');
            const card = document.getElementById('filter-modal-card');
            if (!modal || !backdrop || !card) return;

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            card.classList.remove('opacity-100', 'scale-100');
            card.classList.add('opacity-0', 'scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 250);
        }

        // Pill buttons event binding inside modal
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('#filter-modal [data-filter-group]').forEach(group => {
                const groupName = group.getAttribute('data-filter-group');
                group.querySelectorAll('.filter-pill-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        group.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');

                        const targetInput = document.getElementById(`filter-${groupName}-input`);
                        if (targetInput) {
                            targetInput.value = btn.getAttribute('data-value');
                        }
                    });
                });
            });
        });

        function applyModalFilters() {
            closeFilterModal();
            if (typeof window.pmsTriggerLiveSearch === 'function') {
                window.pmsTriggerLiveSearch();
            }
        }

        function resetModalFilters() {
            // Reset modal pill buttons
            const defaultValues = {
                type: 'all',
                priority: 'all',
                lead: 'all',
                radius: 'all',
                sort: 'newest'
            };

            for (const [groupName, defVal] of Object.entries(defaultValues)) {
                const group = document.querySelector(`#filter-modal [data-filter-group="${groupName}"]`);
                if (group) {
                    group.querySelectorAll('.filter-pill-btn').forEach(btn => {
                        if (btn.getAttribute('data-value') === defVal) {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    });
                }
                const targetInput = document.getElementById(`filter-${groupName}-input`);
                if (targetInput) targetInput.value = defVal;
            }

            closeFilterModal();
            if (typeof window.pmsTriggerLiveSearch === 'function') {
                window.pmsTriggerLiveSearch();
            }
        }

        function clearSpecificFilter(filterKey) {
            const defVal = (filterKey === 'sort') ? 'newest' : 'all';
            const group = document.querySelector(`#filter-modal [data-filter-group="${filterKey}"]`);
            if (group) {
                group.querySelectorAll('.filter-pill-btn').forEach(btn => {
                    if (btn.getAttribute('data-value') === defVal) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }
            const targetInput = document.getElementById(`filter-${filterKey}-input`);
            if (targetInput) targetInput.value = defVal;

            if (typeof window.pmsTriggerLiveSearch === 'function') {
                window.pmsTriggerLiveSearch();
            }
        }

        function confirmDelete(id, name) {
            const modal = document.getElementById('delete-modal');
            const form = document.getElementById('delete-form');
            const nameEl = document.getElementById('delete-party-name');

            form.action = `/parties/${id}`;
            nameEl.innerText = `"${name}"`;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        // --- Accurate Haversine Distance Engine ---
        function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
            const toRad = (x) => (x * Math.PI) / 180;
            const R = 6371; // Earth radius in km
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function formatDistance(distKm) {
            if (distKm == null || isNaN(distKm)) return null;
            if (distKm < 1.0) {
                return Math.round(distKm * 1000) + ' m';
            }
            return distKm.toFixed(1) + ' km';
        }

        function updateAllPartyDistances(userLat = null, userLng = null) {
            const lat = userLat || sessionStorage.getItem('user_lat') || (new URLSearchParams(window.location.search)).get('lat');
            const lng = userLng || sessionStorage.getItem('user_lng') || (new URLSearchParams(window.location.search)).get('lng');
            if (!lat || !lng) return;

            const uLat = parseFloat(lat);
            const uLng = parseFloat(lng);
            if (isNaN(uLat) || isNaN(uLng)) return;

            // Desktop cells
            document.querySelectorAll('.party-distance-cell').forEach(cell => {
                const pLat = parseFloat(cell.getAttribute('data-party-lat'));
                const pLng = parseFloat(cell.getAttribute('data-party-lng'));
                if (!isNaN(pLat) && !isNaN(pLng)) {
                    const dist = calculateHaversineDistance(uLat, uLng, pLat, pLng);
                    const formatted = formatDistance(dist);
                    cell.innerHTML = `
                        <span class="inline-flex items-center gap-1 font-semibold theme-distance-pill px-2 py-0.5 rounded-md text-xs">
                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            ${formatted}
                        </span>
                    `;
                } else {
                    cell.innerHTML = `<span class="text-slate-400 text-xs">No coords</span>`;
                }
            });

            // Mobile cells
            document.querySelectorAll('.party-distance-cell-mobile').forEach(cell => {
                const pLat = parseFloat(cell.getAttribute('data-party-lat'));
                const pLng = parseFloat(cell.getAttribute('data-party-lng'));
                if (!isNaN(pLat) && !isNaN(pLng)) {
                    const dist = calculateHaversineDistance(uLat, uLng, pLat, pLng);
                    const formatted = formatDistance(dist);
                    cell.innerHTML = `
                        <span class="inline-flex items-center gap-1 font-semibold theme-distance-pill px-2 py-0.5 rounded text-xs">
                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            ${formatted} away
                        </span>
                    `;
                } else {
                    cell.innerHTML = `<span class="text-xs text-slate-400">No coordinates</span>`;
                }
            });
        }

        function syncAndComputeLocation() {
            const latInput = document.getElementById('filter-lat');
            const lngInput = document.getElementById('filter-lng');

            let storedLat = sessionStorage.getItem('user_lat');
            let storedLng = sessionStorage.getItem('user_lng');

            const urlParams = new URLSearchParams(window.location.search);
            if (!storedLat && urlParams.get('lat')) storedLat = urlParams.get('lat');
            if (!storedLng && urlParams.get('lng')) storedLng = urlParams.get('lng');

            if (storedLat && storedLng) {
                if (latInput && !latInput.value) latInput.value = storedLat;
                if (lngInput && !lngInput.value) lngInput.value = storedLng;
                updateAllPartyDistances(storedLat, storedLng);
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const curLat = position.coords.latitude;
                        const curLng = position.coords.longitude;
                        sessionStorage.setItem('user_lat', curLat);
                        sessionStorage.setItem('user_lng', curLng);
                        if (latInput) latInput.value = curLat;
                        if (lngInput) lngInput.value = curLng;
                        if (typeof updateGpsUI === 'function') {
                            updateGpsUI(curLat, curLng);
                        }
                        updateAllPartyDistances(curLat, curLng);
                    },
                    (error) => {
                        console.warn('Geolocation auto-detect:', error.message);
                    },
                    { enableHighAccuracy: true, timeout: 8000 }
                );
            }
        }

        // Listen for global location updates from navbar or other components
        window.addEventListener('pms:location-updated', (e) => {
            if (e.detail && e.detail.lat && e.detail.lng) {
                const latInput = document.getElementById('filter-lat');
                const lngInput = document.getElementById('filter-lng');
                if (latInput) latInput.value = e.detail.lat;
                if (lngInput) lngInput.value = e.detail.lng;
                updateAllPartyDistances(e.detail.lat, e.detail.lng);
            }
        });

        // Auto sync GPS inputs from sessionStorage if not currently set in query
        document.addEventListener('DOMContentLoaded', () => {
            syncAndComputeLocation();

            // --- Real-time Live Search Engine ---
            let searchTimer = null;
            let activeAbortController = null;
            const searchInput = document.getElementById('live-search-input');
            const filterForm = document.getElementById('filter-form');
            const spinner = document.getElementById('search-spinner');
            const clearBtn = document.getElementById('search-clear-btn');
            const dataContainer = document.getElementById('parties-data-container');
            const countEl = document.getElementById('matching-count');

            function toggleClearBtn() {
                if (!clearBtn || !searchInput) return;
                if (searchInput.value.trim().length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            async function performLiveSearch(pageUrl = null) {
                if (activeAbortController) {
                    activeAbortController.abort();
                }
                activeAbortController = new AbortController();

                if (spinner) spinner.classList.remove('hidden');
                if (clearBtn) clearBtn.classList.add('hidden');

                // Ensure stored lat/lng are in filter form
                const storedLat = sessionStorage.getItem('user_lat');
                const storedLng = sessionStorage.getItem('user_lng');
                const latInput = document.getElementById('filter-lat');
                const lngInput = document.getElementById('filter-lng');
                if (storedLat && storedLng && latInput && lngInput) {
                    if (!latInput.value) latInput.value = storedLat;
                    if (!lngInput.value) lngInput.value = storedLng;
                }

                let targetUrl = pageUrl;
                if (!targetUrl) {
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData);
                    params.delete('page');
                    targetUrl = `${filterForm.action}?${params.toString()}`;
                }

                try {
                    const response = await fetch(targetUrl, {
                        signal: activeAbortController.signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newContainer = doc.getElementById('parties-data-container');
                    if (newContainer && dataContainer) {
                        dataContainer.innerHTML = newContainer.innerHTML;
                        updateAllPartyDistances();
                    }

                    const newCount = doc.getElementById('matching-count');
                    if (newCount && countEl) {
                        countEl.innerHTML = newCount.innerHTML;
                    }

                    const newChips = doc.getElementById('active-filter-chips');
                    const curChips = document.getElementById('active-filter-chips');
                    if (newChips && curChips) {
                        curChips.innerHTML = newChips.innerHTML;
                    }

                    const newDot = doc.getElementById('filter-active-dot');
                    const curDot = document.getElementById('filter-active-dot');
                    if (newDot && curDot) {
                        curDot.className = newDot.className;
                    }

                    // Sync modal pill active states with current inputs
                    ['type', 'priority', 'radius', 'sort'].forEach(k => {
                        const inp = document.getElementById(`filter-${k}-input`);
                        if (inp) {
                            const val = inp.value;
                            const group = document.querySelector(`#filter-modal [data-filter-group="${k}"]`);
                            if (group) {
                                group.querySelectorAll('.filter-pill-btn').forEach(btn => {
                                    if (btn.getAttribute('data-value') === val) {
                                        btn.classList.add('active');
                                    } else {
                                        btn.classList.remove('active');
                                    }
                                });
                            }
                        }
                    });

                    window.history.replaceState({}, '', targetUrl);
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.error('Live search error:', err);
                    }
                } finally {
                    if (spinner) spinner.classList.add('hidden');
                    toggleClearBtn();
                }
            }

            // Expose globally for modal apply / reset triggers
            window.pmsTriggerLiveSearch = () => performLiveSearch();

            if (searchInput) {
                // Trigger live search on every keystroke
                searchInput.addEventListener('input', () => {
                    toggleClearBtn();
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        performLiveSearch();
                    }, 250);
                });

                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(searchTimer);
                        performLiveSearch();
                    }
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    searchInput.value = '';
                    toggleClearBtn();
                    searchInput.focus();
                    performLiveSearch();
                });
            }

            if (filterForm) {
                // Prevent regular form submission, use seamless live search
                filterForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    clearTimeout(searchTimer);
                    performLiveSearch();
                });

                // Live filter on select changes without page refresh
                filterForm.querySelectorAll('select').forEach(select => {
                    select.removeAttribute('onchange');
                    select.addEventListener('change', () => {
                        clearTimeout(searchTimer);
                        performLiveSearch();
                    });
                });
            }

            // Live pagination clicks
            document.addEventListener('click', (e) => {
                const pageLink = e.target.closest('#parties-data-container nav[role="navigation"] a, #parties-data-container .pagination a');
                if (pageLink && pageLink.href) {
                    e.preventDefault();
                    performLiveSearch(pageLink.href);
                    dataContainer?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>
