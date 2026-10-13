<x-layouts.app title="Interactive Map">
    <x-slot:header>
        <span class="sm:hidden">Map Explorer</span>
        <span class="hidden sm:inline">Interactive Map Explorer</span>
    </x-slot:header>

    <div class="flex-1 flex flex-col relative w-full h-full min-h-0 bg-white">

        <!-- Top Floating Filter Bar -->
        <div id="map-filter-bar" class="p-2.5 sm:p-4 border-b border-slate-200/80 bg-white/95 backdrop-blur-md relative shrink-0 overflow-visible" style="position: relative; z-index: 1200;">
            <form method="GET" action="{{ route('parties.map') }}" id="map-filter-form" class="flex flex-wrap items-center gap-2.5 sm:gap-3 overflow-visible">
                <input type="hidden" name="lat" id="map-lat" value="{{ $userLat }}">
                <input type="hidden" name="lng" id="map-lng" value="{{ $userLng }}">

                <!-- Live Search Input -->
                <div class="relative flex-1 min-w-[180px] max-w-md w-full sm:w-auto">
                    <input type="text" name="search" id="map-live-search" value="{{ $search }}" placeholder="Search parties on map..."
                        autocomplete="off"
                        class="w-full pl-8 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-hidden focus:ring-2 focus:ring-indigo-600/20 transition">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="button" id="map-search-clear" title="Clear search" class="{{ empty($search) ? 'hidden' : '' }} absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Secondary Filters: Theme-styled dropdowns (Clean 2x2 grid on mobile, inline row on desktop) -->
                <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full sm:w-auto overflow-visible">
                    <!-- Priority Filter -->
                    <select name="priority" id="map-priority-select" class="theme-select w-full sm:w-auto px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-hidden">
                        <option value="all" {{ $priority === 'all' ? 'selected' : '' }}>All Priority</option>
                        <option value="priority" {{ $priority === 'priority' ? 'selected' : '' }}>Priority Only</option>
                        <option value="non_priority" {{ $priority === 'non_priority' ? 'selected' : '' }}>Non-Priority</option>
                    </select>

                    <!-- Type Filter -->
                    <select name="type" id="map-type-select" class="theme-select w-full sm:w-auto px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-hidden">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="new" {{ $type === 'new' ? 'selected' : '' }}>New</option>
                        <option value="old" {{ $type === 'old' ? 'selected' : '' }}>Old</option>
                    </select>

                    <!-- Lead Filter -->
                    <select name="lead" id="map-lead-select" class="theme-select w-full sm:w-auto px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-hidden">
                        <option value="all" {{ ($lead ?? 'all') === 'all' ? 'selected' : '' }}>All Leads</option>
                        <option value="lead" {{ ($lead ?? 'all') === 'lead' ? 'selected' : '' }}>Lead</option>
                        <option value="non_lead" {{ ($lead ?? 'all') === 'non_lead' ? 'selected' : '' }}>Non-Lead</option>
                    </select>

                    <!-- Radius Filter -->
                    <select name="radius" id="map-radius-select" class="theme-select w-full sm:w-auto px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-hidden">
                        <option value="all" {{ $radius === 'all' ? 'selected' : '' }}>All Distance</option>
                        <option value="1" {{ $radius === '1' ? 'selected' : '' }}>&le; 1 km</option>
                        <option value="5" {{ $radius === '5' ? 'selected' : '' }}>&le; 5 km</option>
                        <option value="10" {{ $radius === '10' ? 'selected' : '' }}>&le; 10 km</option>
                        <option value="25" {{ $radius === '25' ? 'selected' : '' }}>&le; 25 km</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Main Split Screen Area -->
        <div id="map-split-container" class="flex-1 flex flex-col lg:flex-row relative overflow-hidden w-full h-full min-h-0" style="position: relative; z-index: 1; isolation: isolate;">

            <!-- Interactive Map Pane (Full on Mobile, 65% on Desktop) -->
            <div class="relative flex-1 lg:w-2/3 w-full h-full min-h-[300px] bg-slate-100 overflow-hidden p-2">
                <div id="full-map" class="w-full h-full min-h-full" style="width: 100%; height: 100%;"></div>

                <!-- Floating Map Floating Controls -->
                <div class="absolute top-4 right-4 z-400 flex flex-col gap-2">
                    <button id="recenter-btn" type="button" title="Center to My Location" class="w-9 h-9 rounded-xl bg-white/95 backdrop-blur-xs border border-slate-200/80 shadow-md text-slate-700 hover:text-indigo-600 flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                    <button id="fit-bounds-btn" type="button" title="Fit All Parties" class="w-9 h-9 rounded-xl bg-white/95 backdrop-blur-xs border border-slate-200/80 shadow-md text-slate-700 hover:text-indigo-600 flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </button>
                </div>

                <!-- Map Legend -->
                <div class="absolute bottom-4 left-4 z-400 bg-white/95 backdrop-blur-xs px-3 py-2 rounded-xl border border-slate-200/80 shadow-sm text-xs flex items-center gap-3">
                    <span class="flex items-center gap-1.5 font-medium text-slate-700">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Priority
                    </span>
                    <span class="flex items-center gap-1.5 font-medium text-slate-700">
                        <span class="w-3 h-3 rounded-full bg-slate-500"></span> Non-Priority
                    </span>
                    @if($hasLocation)
                        <span class="flex items-center gap-1.5 font-medium text-blue-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span> You
                        </span>
                    @endif
                </div>
            </div>

            <!-- Side Party Explorer List (Desktop Split, ~35%) -->
            <div class="hidden lg:flex lg:flex-col w-1/3 border-l border-slate-200/80 bg-white h-full z-10">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Parties in View</h3>
                        <p class="text-[11px] text-slate-400"><span id="map-parties-count">{{ count($mapPins) }}</span> party locations mapped</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600">Click to focus pin</span>
                </div>

                <div id="map-party-list" class="flex-1 overflow-y-auto divide-y divide-slate-100">
                    @forelse($parties as $party)
                        <div onclick="focusMarker({{ $party->id }})" 
                             data-party-id="{{ $party->id }}"
                             data-search-text="{{ strtolower($party->party_name . ' ' . ($party->mobile_number ?? '')) }}"
                             data-priority="{{ $party->priority_status }}"
                             data-type="{{ $party->party_type }}"
                             data-lead="{{ $party->is_lead ? 'lead' : 'non_lead' }}"
                             data-distance="{{ isset($party->distance) ? (float) $party->distance : '' }}"
                             data-lat="{{ $party->latitude }}"
                             data-lng="{{ $party->longitude }}"
                             class="map-party-card p-4 hover:bg-slate-50/80 cursor-pointer transition group">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">{{ $party->party_name }}</h4>
                                    @if($party->is_lead)
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200/80 theme-lead-badge">Lead</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($party->isPriority())
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Priority</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">Normal</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                <div>
                                    @if(isset($party->distance))
                                        <span class="font-semibold text-xs theme-distance-pill px-2.5 py-1 rounded-lg">{{ $party->formatted_distance }}</span>
                                    @elseif($party->mobile_number)
                                        <span class="font-mono text-[11px]">{{ $party->mobile_number }}</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5" onclick="event.stopPropagation()">
                                    @if($party->google_navigation_url)
                                        <a href="{{ $party->google_navigation_url }}" target="_blank" rel="noopener noreferrer" class="px-2 py-1 rounded bg-indigo-600 text-white text-[11px] font-semibold hover:bg-indigo-700">Navigate</a>
                                    @endif
                                    <a href="{{ route('parties.edit', $party->id) }}" class="px-2 py-1 rounded bg-slate-100 text-slate-700 text-[11px] hover:bg-slate-200">Edit</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No parties match the filter criteria.
                        </div>
                    @endforelse
                    <div id="no-parties-search-msg" class="hidden p-8 text-center text-slate-400 text-xs">
                        No parties match your search.
                    </div>
                </div>
            </div>

        </div>

        <!-- Mobile Bottom Sheet for Selected Party (pops up on pin click) -->
        <div id="mobile-sheet" class="hidden lg:hidden fixed bottom-16 left-0 right-0 z-40 bg-white border-t border-slate-200 p-4 shadow-2xl transition-all duration-300">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span id="sheet-lead-badge" class="hidden px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200/80 theme-lead-badge">Lead</span>
                        <span id="sheet-priority-badge" class="px-2 py-0.5 rounded-full text-[10px] font-semibold"></span>
                        <span id="sheet-type-badge" class="px-2 py-0.5 rounded-full text-[10px] font-medium"></span>
                        <span id="sheet-distance" class="px-2 py-0.5 rounded-full text-[10px] font-semibold text-indigo-700 bg-indigo-50"></span>
                    </div>
                    <h3 id="sheet-title" class="text-base font-bold text-slate-900 mt-1 truncate"></h3>
                    <p id="sheet-mobile" class="text-xs text-slate-500 font-mono mt-0.5"></p>
                </div>
                <button onclick="closeMobileSheet()" class="p-1 text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <a id="sheet-navigate-btn" href="#" target="_blank" class="flex-1 py-2 text-center rounded-xl bg-indigo-600 text-white text-xs font-semibold shadow-xs">
                    Navigate (Google Maps)
                </a>
                <a id="sheet-edit-btn" href="#" class="px-4 py-2 text-center rounded-xl bg-slate-100 text-slate-700 text-xs font-medium">
                    Edit
                </a>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        let map;
        const markersById = {};
        const mapPins = @json($mapPins);
        const userLat = @json($userLat);
        const userLng = @json($userLng);

        document.addEventListener('DOMContentLoaded', function () {
            let initialLat = 12.9716;
            let initialLng = 77.5946;

            if (userLat && userLng) {
                initialLat = userLat;
                initialLng = userLng;
            } else if (mapPins.length > 0) {
                initialLat = mapPins[0].lat;
                initialLng = mapPins[0].lng;
            }

            map = L.map('full-map', {
                zoomControl: true,
                attributionControl: false
            }).setView([initialLat, initialLng], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            const allBounds = [];

            // User location pin
            if (userLat && userLng) {
                const userIcon = L.divIcon({
                    className: 'user-pulse-marker-wrapper',
                    html: '<div class="user-pulse-marker"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });
                L.marker([userLat, userLng], { icon: userIcon })
                    .bindPopup('<b>You are here</b>')
                    .addTo(map);
                allBounds.push([userLat, userLng]);
            }

            // Party pin generator
            function createPinIcon(isPriority) {
                const color = isPriority ? '#10b981' : '#64748b';
                const svg = `<svg width="30" height="38" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 0C5.37258 0 0 5.37258 0 12C0 20.25 12 32 12 32C12 32 24 20.25 24 12C24 5.37258 18.6274 0 12 0Z" fill="${color}"/>
                    <circle cx="12" cy="12" r="5" fill="white"/>
                </svg>`;
                return L.divIcon({
                    html: svg,
                    className: 'party-map-marker',
                    iconSize: [30, 38],
                    iconAnchor: [15, 38],
                    popupAnchor: [0, -34]
                });
            }

            mapPins.forEach(pin => {
                if (pin.lat && pin.lng) {
                    const isPriority = (pin.priority === 'priority');
                    const marker = L.marker([pin.lat, pin.lng], { icon: createPinIcon(isPriority) });

                    let popupContent = `
                        <div class="p-1 min-w-[190px]">
                            <h4 class="font-bold text-sm text-slate-900 leading-tight">${pin.name}</h4>
                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                ${pin.is_lead ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800 border border-purple-200 theme-lead-badge">LEAD</span>` : ''}
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold ${isPriority ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'}">
                                    ${isPriority ? 'Priority' : 'Normal'}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] bg-blue-100 text-blue-800">${pin.type.toUpperCase()}</span>
                                ${pin.distance ? `<span class="px-2 py-0.5 rounded text-[10px] bg-indigo-100 text-indigo-800 font-semibold">${pin.distance}</span>` : ''}
                            </div>
                            ${pin.mobile ? `<p class="mt-2 text-xs text-slate-600"><a href="tel:${pin.mobile}" class="text-indigo-600 hover:underline">📞 ${pin.mobile}</a></p>` : ''}
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center gap-2">
                                ${pin.nav_url ? `<a href="${pin.nav_url}" target="_blank" class="px-3 py-1 rounded bg-indigo-600 text-white text-[11px] font-semibold">Navigate</a>` : ''}
                                <a href="${pin.edit_url}" class="px-3 py-1 rounded bg-slate-100 text-slate-700 text-[11px]">Edit</a>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent);

                    // Marker click handler: also updates mobile bottom sheet
                    marker.on('click', () => {
                        openMobileSheet(pin);
                    });

                    marker.addTo(map);
                    markersById[pin.id] = marker;
                    allBounds.push([pin.lat, pin.lng]);
                }
            });

            if (allBounds.length > 0) {
                map.fitBounds(allBounds, { padding: [40, 40], maxZoom: 14 });
            }

            // Ensure proper sizing in all viewport modes & orientations
            setTimeout(() => { if (map) map.invalidateSize(); }, 150);
            setTimeout(() => { if (map) map.invalidateSize(); }, 500);
            window.addEventListener('resize', () => { if (map) map.invalidateSize(); });
            window.addEventListener('orientationchange', () => { setTimeout(() => { if (map) map.invalidateSize(); }, 200); });

            // Recenter to user
            document.getElementById('recenter-btn').addEventListener('click', () => {
                if (userLat && userLng) {
                    map.setView([userLat, userLng], 14);
                } else {
                    detectUserLocation();
                }
            });

            // Fit all pins
            document.getElementById('fit-bounds-btn').addEventListener('click', () => {
                if (allBounds.length > 0) {
                    map.fitBounds(allBounds, { padding: [40, 40] });
                }
            });

            // Live Search & Multi-Criteria Filtering for Map and Sidebar
            const mapSearchInput = document.getElementById('map-live-search');
            const mapSearchClearBtn = document.getElementById('map-search-clear');
            const mapPartyCards = document.querySelectorAll('.map-party-card');
            const mapPartiesCount = document.getElementById('map-parties-count');
            const noPartiesSearchMsg = document.getElementById('no-parties-search-msg');

            const prioritySelect = document.getElementById('map-priority-select');
            const typeSelect = document.getElementById('map-type-select');
            const leadSelect = document.getElementById('map-lead-select');
            const radiusSelect = document.getElementById('map-radius-select');

            function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
                const toRad = (x) => (x * Math.PI) / 180;
                const R = 6371;
                const dLat = toRad(lat2 - lat1);
                const dLon = toRad(lon2 - lon1);
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                          Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                          Math.sin(dLon / 2) * Math.sin(dLon / 2);
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function filterMapLive() {
                const query = (mapSearchInput?.value || '').trim().toLowerCase();
                const priorityVal = prioritySelect?.value || 'all';
                const typeVal = typeSelect?.value || 'all';
                const leadVal = leadSelect?.value || 'all';
                const radiusVal = radiusSelect?.value || 'all';
                const maxRadius = (radiusVal !== 'all' && !isNaN(parseFloat(radiusVal))) ? parseFloat(radiusVal) : null;

                if (mapSearchClearBtn) {
                    mapSearchClearBtn.classList.toggle('hidden', query === '');
                }

                let userLat = parseFloat(document.getElementById('map-lat')?.value) || parseFloat(sessionStorage.getItem('user_lat'));
                let userLng = parseFloat(document.getElementById('map-lng')?.value) || parseFloat(sessionStorage.getItem('user_lng'));

                let visibleCount = 0;
                const visibleBounds = [];

                mapPins.forEach(pin => {
                    const marker = markersById[pin.id];
                    const pinName = (pin.name || '').toLowerCase();
                    const pinMobile = (pin.mobile || '').toLowerCase();

                    // Search text match
                    const matchesQuery = !query || pinName.includes(query) || pinMobile.includes(query);

                    // Priority match
                    const matchesPriority = (priorityVal === 'all') || (pin.priority === priorityVal);

                    // Type match
                    const matchesType = (typeVal === 'all') || (pin.type === typeVal);

                    // Lead match
                    const matchesLead = (leadVal === 'all') || (leadVal === 'lead' && pin.is_lead) || (leadVal === 'non_lead' && !pin.is_lead);

                    // Radius / Distance match
                    let matchesRadius = true;
                    if (maxRadius !== null) {
                        let dist = pin.distance_km;
                        if ((dist == null || isNaN(dist)) && !isNaN(userLat) && !isNaN(userLng) && pin.lat && pin.lng) {
                            dist = calculateHaversineDistance(userLat, userLng, parseFloat(pin.lat), parseFloat(pin.lng));
                        }
                        matchesRadius = (dist != null && !isNaN(dist) && dist <= maxRadius);
                    }

                    const matches = matchesQuery && matchesPriority && matchesType && matchesLead && matchesRadius;

                    if (marker) {
                        if (matches) {
                            if (!map.hasLayer(marker)) {
                                marker.addTo(map);
                            }
                            if (pin.lat && pin.lng) {
                                visibleBounds.push([pin.lat, pin.lng]);
                            }
                            visibleCount++;
                        } else {
                            if (map.hasLayer(marker)) {
                                map.removeLayer(marker);
                            }
                        }
                    }
                });

                // Filter sidebar items
                mapPartyCards.forEach(card => {
                    const text = (card.getAttribute('data-search-text') || '').toLowerCase();
                    const cardPriority = card.getAttribute('data-priority');
                    const cardType = card.getAttribute('data-type');
                    const cardDist = parseFloat(card.getAttribute('data-distance'));
                    const cardLat = parseFloat(card.getAttribute('data-lat'));
                    const cardLng = parseFloat(card.getAttribute('data-lng'));

                    const matchesQuery = !query || text.includes(query);
                    const matchesPriority = (priorityVal === 'all') || (cardPriority === priorityVal);
                    const matchesType = (typeVal === 'all') || (cardType === typeVal);
                    const cardLead = card.getAttribute('data-lead');
                    const matchesLead = (leadVal === 'all') || (cardLead === leadVal);

                    let matchesRadius = true;
                    if (maxRadius !== null) {
                        let dist = !isNaN(cardDist) ? cardDist : null;
                        if (dist == null && !isNaN(userLat) && !isNaN(userLng) && !isNaN(cardLat) && !isNaN(cardLng)) {
                            dist = calculateHaversineDistance(userLat, userLng, cardLat, cardLng);
                        }
                        matchesRadius = (dist != null && !isNaN(dist) && dist <= maxRadius);
                    }

                    const cardMatches = matchesQuery && matchesPriority && matchesType && matchesLead && matchesRadius;
                    card.classList.toggle('hidden', !cardMatches);
                });

                if (mapPartiesCount) {
                    mapPartiesCount.innerText = visibleCount;
                }

                if (noPartiesSearchMsg) {
                    noPartiesSearchMsg.classList.toggle('hidden', visibleCount > 0 || mapPins.length === 0);
                }

                // Smoothly fit bounds to active markers
                if ((query.length > 0 || priorityVal !== 'all' || typeVal !== 'all' || leadVal !== 'all' || radiusVal !== 'all') && visibleBounds.length > 0) {
                    map.fitBounds(visibleBounds, { padding: [50, 50], maxZoom: 15 });
                } else if (allBounds.length > 0) {
                    map.fitBounds(allBounds, { padding: [40, 40], maxZoom: 14 });
                }

                // Sync URL parameters without full page reload
                const url = new URL(window.location);
                if (query) url.searchParams.set('search', mapSearchInput.value.trim()); else url.searchParams.delete('search');
                if (priorityVal !== 'all') url.searchParams.set('priority', priorityVal); else url.searchParams.delete('priority');
                if (typeVal !== 'all') url.searchParams.set('type', typeVal); else url.searchParams.delete('type');
                if (leadVal !== 'all') url.searchParams.set('lead', leadVal); else url.searchParams.delete('lead');
                if (radiusVal !== 'all') url.searchParams.set('radius', radiusVal); else url.searchParams.delete('radius');
                window.history.replaceState({}, '', url);
            }

            if (mapSearchInput) {
                // Instantly filter on every keystroke
                mapSearchInput.addEventListener('input', filterMapLive);
                mapSearchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        filterMapLive();
                    }
                });
            }

            if (mapSearchClearBtn) {
                mapSearchClearBtn.addEventListener('click', () => {
                    if (mapSearchInput) {
                        mapSearchInput.value = '';
                        mapSearchInput.focus();
                    }
                    filterMapLive();
                });
            }

            // Instantly filter on select dropdown changes
            [prioritySelect, typeSelect, leadSelect, radiusSelect].forEach(select => {
                if (select) {
                    select.addEventListener('change', filterMapLive);
                }
            });

            const mapFilterForm = document.getElementById('map-filter-form');
            if (mapFilterForm) {
                mapFilterForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    filterMapLive();
                });
            }

            // Run initial filter if any criteria is preset
            if ((mapSearchInput && mapSearchInput.value.trim() !== '') || prioritySelect?.value !== 'all' || typeSelect?.value !== 'all' || leadSelect?.value !== 'all' || radiusSelect?.value !== 'all') {
                filterMapLive();
            }
        });

        function focusMarker(id) {
            const marker = markersById[id];
            if (marker && map) {
                map.setView(marker.getLatLng(), 15);
                marker.openPopup();
            }
        }

        // Mobile Bottom Sheet
        function openMobileSheet(pin) {
            const sheet = document.getElementById('mobile-sheet');
            document.getElementById('sheet-title').innerText = pin.name;
            document.getElementById('sheet-mobile').innerText = pin.mobile ? `Tel: ${pin.mobile}` : 'No phone recorded';
            
            const isPriority = pin.priority === 'priority';
            const leadBadge = document.getElementById('sheet-lead-badge');
            if (leadBadge) {
                leadBadge.classList.toggle('hidden', !pin.is_lead);
            }

            const priorityBadge = document.getElementById('sheet-priority-badge');
            priorityBadge.innerText = isPriority ? 'Priority' : 'Normal';
            priorityBadge.className = `px-2 py-0.5 rounded-full text-[10px] font-semibold ${isPriority ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700'}`;

            const typeBadge = document.getElementById('sheet-type-badge');
            typeBadge.innerText = pin.type.toUpperCase();
            typeBadge.className = `px-2 py-0.5 rounded-full text-[10px] font-medium ${pin.type === 'new' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700'}`;

            const distEl = document.getElementById('sheet-distance');
            if (pin.distance) {
                distEl.innerText = pin.distance;
                distEl.classList.remove('hidden');
            } else {
                distEl.classList.add('hidden');
            }

            document.getElementById('sheet-navigate-btn').href = pin.nav_url || '#';
            document.getElementById('sheet-edit-btn').href = pin.edit_url || '#';

            sheet.classList.remove('hidden');
        }

        function closeMobileSheet() {
            document.getElementById('mobile-sheet').classList.add('hidden');
        }
    </script>
    @endpush
</x-layouts.app>
