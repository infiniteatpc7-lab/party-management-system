<x-layouts.app title="Dashboard">
    <x-slot:header>
        Dashboard Overview
    </x-slot:header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Welcome Banner & GPS Status -->
        <div class="relative overflow-hidden bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        @if($hasLocation)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                GPS Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                GPS Idle
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-2">Party Management & Nearby Finder</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                        Monitor, filter, and navigate to client parties. View real-time distances from your browser's GPS or search records.
                    </p>
                </div>

                <!-- Action Group -->
                <div class="flex items-center gap-3 shrink-0">
                    @if(!$hasLocation)
                        <button onclick="detectUserLocation()" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 transition">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Enable GPS Distances</span>
                        </button>
                    @endif
                    <a href="{{ route('parties.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Party</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4 Core KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
            <!-- 1. Total Parties -->
            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 flex items-start sm:items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 truncate">Total Parties</p>
                    <p class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-0.5 sm:mt-1">{{ number_format($totalParties) }}</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 truncate">Across all categories</p>
                </div>
                <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 kpi-icon-box flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>

            <!-- 2. Nearby Parties -->
            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 flex items-start sm:items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 truncate">
                        {{ $hasLocation ? 'Nearby (< 10 km)' : 'Geocoded Parties' }}
                    </p>
                    <p class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-0.5 sm:mt-1">{{ number_format($nearbyPartiesCount) }}</p>
                    <p class="text-[10px] sm:text-[11px] {{ $hasLocation ? 'text-emerald-600' : 'text-slate-400' }} mt-0.5 sm:mt-1 truncate">
                        {{ $hasLocation ? 'Calculated via GPS' : 'With map coordinates' }}
                    </p>
                </div>
                <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 kpi-icon-box flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>

            <!-- 3. Priority Parties -->
            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 flex items-start sm:items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 truncate">Priority Parties</p>
                    <p class="text-xl sm:text-2xl font-bold tracking-tight text-emerald-700 mt-0.5 sm:mt-1">{{ number_format($priorityParties) }}</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 truncate">Designated high-priority</p>
                </div>
                <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 kpi-icon-box flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- 4. Non-Priority Parties -->
            <div class="bg-white p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 flex items-start sm:items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 truncate">Non-Priority</p>
                    <p class="text-xl sm:text-2xl font-bold tracking-tight text-slate-700 mt-0.5 sm:mt-1">{{ number_format($nonPriorityParties) }}</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 truncate">Standard party records</p>
                </div>
                <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-slate-100 text-slate-600 kpi-icon-box flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Dashboard Content Grid: Map Preview (Left) & Nearby Parties (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Interactive Map Preview (7 Cols) -->
            <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-2xl overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-white">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Map Preview</h3>
                        <p class="text-xs text-slate-400">Pins colored by Priority (Green) and Non-Priority (Grey)</p>
                    </div>
                    <a href="{{ route('parties.map') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                        <span>Full Map</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Leaflet Container -->
                <div class="relative w-full h-80 sm:h-96 bg-slate-100">
                    <div id="dashboard-map" class="w-full h-full"></div>
                    <div class="absolute bottom-3 right-3 z-400 bg-white/90 backdrop-blur-xs px-3 py-1.5 rounded-xl border border-slate-200/80 text-[11px] flex items-center gap-3">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Priority
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span> Non-Priority
                        </span>
                    </div>
                </div>
            </div>

            <!-- Nearby Party List Preview (5 Cols) -->
            <div class="lg:col-span-5 bg-white border border-slate-200/80 rounded-2xl overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-white">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ $hasLocation ? 'Closest Parties' : 'Recent Parties' }}</h3>
                        <p class="text-xs text-slate-400">
                            {{ $hasLocation ? 'Sorted by real-time GPS distance' : 'Click "Detect GPS" for distance' }}
                        </p>
                    </div>
                    <a href="{{ route('parties.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                        View All
                    </a>
                </div>

                <div class="divide-y divide-slate-100 overflow-y-auto max-h-96">
                    @forelse($nearbyParties as $party)
                        <div class="p-4 hover:bg-slate-50/70 transition flex items-start justify-between gap-3 group">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $party->party_name }}</h4>
                                    <!-- Priority Badge -->
                                    @if($party->isPriority())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            Priority
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Normal
                                        </span>
                                    @endif

                                    <!-- Type Badge -->
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $party->isNew() ? 'bg-blue-50 text-blue-700 border border-blue-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60' }}">
                                        {{ ucfirst($party->party_type) }}
                                    </span>
                                </div>

                                <div class="mt-1 flex items-center gap-3 text-xs text-slate-500">
                                    @if($party->mobile_number)
                                        <span class="flex items-center gap-1 text-[11px]">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $party->mobile_number }}
                                        </span>
                                    @endif

                                    @if(isset($party->distance))
                                        <span class="inline-flex items-center gap-1 font-semibold text-indigo-600 text-[11px] bg-indigo-50 px-2 py-0.5 rounded-md">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                            {{ $party->formatted_distance }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-1 shrink-0">
                                @if($party->google_navigation_url)
                                    <a href="{{ $party->google_navigation_url }}" target="_blank" rel="noopener noreferrer" title="Navigate in Google Maps" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-white rounded-lg border border-transparent hover:border-slate-200 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('parties.edit', $party->id) }}" title="Edit Party" class="p-1.5 text-slate-400 hover:text-slate-800 hover:bg-white rounded-lg border border-transparent hover:border-slate-200 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No party records found. Click "Add Party" or "Import" to populate.
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Need to batch upload records?</span>
                    <a href="{{ route('import.index') }}" class="font-semibold text-indigo-600 hover:underline">Import Excel File &rarr;</a>
                </div>
            </div>

        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapPins = @json($mapPins);
            const userLat = @json($userLat);
            const userLng = @json($userLng);

            // Default center if no parties: Bangalore center or first pin
            let defaultLat = 12.9716;
            let defaultLng = 77.5946;

            if (userLat && userLng) {
                defaultLat = userLat;
                defaultLng = userLng;
            } else if (mapPins.length > 0) {
                defaultLat = mapPins[0].lat;
                defaultLng = mapPins[0].lng;
            }

            const map = L.map('dashboard-map', {
                zoomControl: true,
                attributionControl: false
            }).setView([defaultLat, defaultLng], 12);

            // OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            const bounds = [];

            // Add Current User Location Pin if available
            if (userLat && userLng) {
                const userIcon = L.divIcon({
                    className: 'custom-user-icon',
                    html: '<div class="user-pulse-marker"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });
                L.marker([userLat, userLng], { icon: userIcon })
                    .bindPopup('<b>Your Current Location</b>')
                    .addTo(map);
                bounds.push([userLat, userLng]);
            }

            // Custom markers for parties: Emerald for Priority, Slate for Non-Priority
            function createPinIcon(isPriority) {
                const color = isPriority ? '#10b981' : '#64748b';
                const svg = `<svg width="28" height="36" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 0C5.37258 0 0 5.37258 0 12C0 20.25 12 32 12 32C12 32 24 20.25 24 12C24 5.37258 18.6274 0 12 0Z" fill="${color}"/>
                    <circle cx="12" cy="12" r="5" fill="white"/>
                </svg>`;
                return L.divIcon({
                    html: svg,
                    className: 'party-marker-icon',
                    iconSize: [28, 36],
                    iconAnchor: [14, 36],
                    popupAnchor: [0, -32]
                });
            }

            mapPins.forEach(pin => {
                if (pin.lat && pin.lng) {
                    const isPriority = (pin.priority === 'priority');
                    const marker = L.marker([pin.lat, pin.lng], { icon: createPinIcon(isPriority) });

                    let popupContent = `
                        <div class="p-1 text-slate-800">
                            <h4 class="font-bold text-sm text-slate-900">${pin.name}</h4>
                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold ${isPriority ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'}">
                                    ${isPriority ? 'Priority' : 'Normal'}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] bg-blue-100 text-blue-800">${pin.type.toUpperCase()}</span>
                                ${pin.distance ? `<span class="px-2 py-0.5 rounded text-[10px] bg-indigo-100 text-indigo-800 font-semibold">${pin.distance}</span>` : ''}
                            </div>
                            ${pin.mobile ? `<p class="mt-1.5 text-xs text-slate-600">Tel: ${pin.mobile}</p>` : ''}
                            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center gap-2">
                                ${pin.nav_url ? `<a href="${pin.nav_url}" target="_blank" class="px-2.5 py-1 rounded bg-indigo-600 text-white text-[11px] font-semibold">Navigate</a>` : ''}
                                <a href="/parties/${pin.id}/edit" class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-[11px]">Edit</a>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                    marker.addTo(map);
                    bounds.push([pin.lat, pin.lng]);
                }
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [30, 30], maxZoom: 14 });
            }
        });
    </script>
    @endpush
</x-layouts.app>
