<x-layouts.app title="Add New Party">
    <x-slot:header>
        Add New Party
    </x-slot:header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Back Button & Title -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('parties.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">Create Party Record</h2>
                    <p class="text-xs text-slate-500">Provide details and pin party location on the interactive map.</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('parties.store') }}" id="party-form" class="space-y-6">
                @csrf

                <!-- Basic Information Section -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">1. Basic Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Party Name -->
                        <div class="sm:col-span-2">
                            <label for="party_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Party Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="party_name" id="party_name" required
                                value="{{ old('party_name') }}"
                                placeholder="e.g. Grand Metropolitan Hotel & Convention"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-hidden focus:ring-2 focus:ring-indigo-600/20 transition @error('party_name') border-rose-500 @enderror">
                            @error('party_name')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label for="mobile_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Mobile Number <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="mobile_number" id="mobile_number"
                                value="{{ old('mobile_number') }}"
                                placeholder="e.g. +91 9876543210"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-hidden focus:ring-2 focus:ring-indigo-600/20 transition @error('mobile_number') border-rose-500 @enderror">
                            @error('mobile_number')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Segmented Selectors: Party Type & Priority -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Party Type <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-xl gap-1 theme-segment-wrapper">
                                <label class="cursor-pointer">
                                    <input type="radio" name="party_type" value="new" class="sr-only peer" {{ old('party_type', 'new') === 'new' ? 'checked' : '' }}>
                                    <div class="theme-segment-btn py-2 text-center text-xs font-semibold rounded-lg text-slate-600 peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-xs transition">
                                        New Party
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="party_type" value="old" class="sr-only peer" {{ old('party_type') === 'old' ? 'checked' : '' }}>
                                    <div class="theme-segment-btn py-2 text-center text-xs font-semibold rounded-lg text-slate-600 peer-checked:bg-white peer-checked:text-amber-600 peer-checked:shadow-xs transition">
                                        Old Party
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Priority Status -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Priority Status <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-xl gap-1 theme-segment-wrapper">
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority_status" value="priority" class="sr-only peer" {{ old('priority_status') === 'priority' ? 'checked' : '' }}>
                                    <div class="theme-segment-btn py-2 text-center text-xs font-semibold rounded-lg text-slate-600 peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-xs transition flex items-center justify-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 theme-priority-dot theme-priority-dot-pri"></span> Priority
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority_status" value="non_priority" class="sr-only peer" {{ old('priority_status', 'non_priority') === 'non_priority' ? 'checked' : '' }}>
                                    <div class="theme-segment-btn py-2 text-center text-xs font-semibold rounded-lg text-slate-600 peer-checked:bg-white peer-checked:text-slate-800 peer-checked:shadow-xs transition flex items-center justify-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-slate-400 theme-priority-dot theme-priority-dot-non"></span> Non-Priority
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Lead Field (Checkbox) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Lead
                            </label>
                            <div class="h-[42px] px-3.5 bg-slate-100 rounded-xl flex items-center theme-segment-wrapper">
                                <label for="is_lead" class="inline-flex items-center gap-2.5 cursor-pointer select-none w-full">
                                    <input type="checkbox" name="is_lead" id="is_lead" value="1" {{ old('is_lead') ? 'checked' : '' }}
                                        class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer accent-indigo-600 theme-lead-checkbox">
                                    <span class="text-xs font-medium text-slate-700">Client Lead from Source</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location & GPS Coordinates Section -->
                <div class="pt-6 border-t border-slate-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">2. Geographical Location</h3>
                        <span class="text-xs text-indigo-600 font-medium">Click on map or paste Google Maps link</span>
                    </div>

                    <!-- Google Maps Link -->
                    <div>
                        <label for="map_link" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Google Maps Link <span class="text-slate-400 font-normal">(Auto-extracts lat/long)</span>
                        </label>
                        <div class="relative">
                            <input type="url" name="map_link" id="map_link"
                                value="{{ old('map_link') }}"
                                placeholder="https://maps.google.com/?q=12.9715987,77.5945627"
                                class="w-full pl-9 pr-24 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-hidden focus:ring-2 focus:ring-indigo-600/20 transition @error('map_link') border-rose-500 @enderror">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </div>
                            <button type="button" id="extract-coords-btn" class="absolute inset-y-1.5 right-1.5 px-3 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-semibold transition">
                                Extract
                            </button>
                        </div>
                        @error('map_link')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Direct Coordinates -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="latitude" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Latitude <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="any" name="latitude" id="latitude" required
                                value="{{ old('latitude', '12.9715987') }}"
                                placeholder="12.9715987"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono text-slate-900 focus:border-indigo-600 focus:outline-hidden focus:ring-2 focus:ring-indigo-600/20 transition @error('latitude') border-rose-500 @enderror">
                            @error('latitude')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="longitude" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Longitude <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="any" name="longitude" id="longitude" required
                                value="{{ old('longitude', '77.5945627') }}"
                                placeholder="77.5945627"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono text-slate-900 focus:border-indigo-600 focus:outline-hidden focus:ring-2 focus:ring-indigo-600/20 transition @error('longitude') border-rose-500 @enderror">
                            @error('longitude')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Interactive Pin Placement Map -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-700">Interactive Location Picker</span>
                            <span class="text-[11px] text-slate-400">Click anywhere on map to drop pin</span>
                        </div>
                        <div class="h-64 sm:h-72 w-full rounded-xl overflow-hidden border border-slate-200 shadow-inner bg-slate-100">
                            <div id="picker-map" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('parties.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm shadow-indigo-600/25 transition">
                        Save Party
                    </button>
                </div>
            </form>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const mapLinkInput = document.getElementById('map_link');

            let initialLat = parseFloat(latInput.value) || 12.9715987;
            let initialLng = parseFloat(lngInput.value) || 77.5945627;

            const map = L.map('picker-map', {
                zoomControl: true,
                attributionControl: false
            }).setView([initialLat, initialLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

            function updateMarker(lat, lng) {
                lat = parseFloat(lat);
                lng = parseFloat(lng);
                if (!isNaN(lat) && !isNaN(lng)) {
                    marker.setLatLng([lat, lng]);
                    map.panTo([lat, lng]);
                    latInput.value = lat.toFixed(7);
                    lngInput.value = lng.toFixed(7);
                }
            }

            // Click to drop pin
            map.on('click', function (e) {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });

            // Drag pin
            marker.on('dragend', function (e) {
                const pos = marker.getLatLng();
                latInput.value = pos.lat.toFixed(7);
                lngInput.value = pos.lng.toFixed(7);
            });

            // Input changes
            latInput.addEventListener('input', () => updateMarker(latInput.value, lngInput.value));
            lngInput.addEventListener('input', () => updateMarker(latInput.value, lngInput.value));

            // Extract coordinates from map link
            function parseCoordinatesFromLink(text) {
                if (!text) return null;

                // Match !3dlat!4dlng (exact place pin location)
                let match = text.match(/!3d(-?\d{1,2}\.\d+)!4d(-?\d{1,3}\.\d+)/);
                if (match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };

                // Match ?q=lat,lng
                match = text.match(/[?&](?:q|destination|center)=(-?\d{1,2}\.\d+),(-?\d{1,3}\.\d+)/);
                if (match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };

                // Match @lat,lng
                match = text.match(/@(-?\d{1,2}\.\d+),(-?\d{1,3}\.\d+)/);
                if (match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };

                // Match direct coordinates "12.34, 56.78"
                match = text.match(/^(-?\d{1,2}(?:\.\d+)?)[,\s]+(-?\d{1,3}(?:\.\d+)?)$/);
                if (match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };

                return null;
            }

            async function tryExtract() {
                const text = mapLinkInput.value.trim();
                if (!text) return;

                // Fast client-side regex check
                const coords = parseCoordinatesFromLink(text);
                if (coords) {
                    updateMarker(coords.lat, coords.lng);
                    return;
                }

                // Server-side resolution (handles maps.app.goo.gl and redirects)
                const btn = document.getElementById('extract-coords-btn');
                const origText = btn ? btn.innerText : '';
                if (btn) {
                    btn.disabled = true;
                    btn.innerText = 'Extracting...';
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch('{{ route("parties.extract_coordinates") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ url: text })
                    });
                    const data = await res.json();
                    if (data.success && data.latitude && data.longitude) {
                        updateMarker(data.latitude, data.longitude);
                    } else if (btn) {
                        btn.innerText = 'Not found';
                        setTimeout(() => { if (btn) btn.innerText = origText; }, 2000);
                    }
                } catch (e) {
                    console.error('Failed to extract coordinates from server', e);
                } finally {
                    if (btn && btn.innerText === 'Extracting...') {
                        btn.innerText = origText;
                    }
                    if (btn) btn.disabled = false;
                }
            }

            document.getElementById('extract-coords-btn').addEventListener('click', tryExtract);
            mapLinkInput.addEventListener('paste', () => setTimeout(tryExtract, 150));
            mapLinkInput.addEventListener('change', tryExtract);
        });
    </script>
    @endpush
</x-layouts.app>
