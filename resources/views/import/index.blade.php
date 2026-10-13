<x-layouts.app title="Import Excel / CSV">
    <x-slot:header>
        Batch Import Parties
    </x-slot:header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Top Header & Sample Template Download -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-slate-900">Import Parties via Spreadsheet</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Upload a .xlsx or .csv spreadsheet to batch insert parties.</p>
            </div>

            <a href="{{ route('import.template') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200/80 shadow-xs transition">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Download Sample Template (.csv)</span>
            </a>
        </div>

        <!-- Import Summary Cards (Shown if import was just processed) -->
        @if(session('import_result'))
            @php $res = session('import_result'); @endphp
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Import Results Summary</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                    <!-- Total Rows -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Rows</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($res['total']) }}</p>
                    </div>

                    <!-- Successfully Imported -->
                    <div class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-xs bg-emerald-50/20">
                        <p class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Imported</p>
                        <p class="text-2xl font-bold text-emerald-700 mt-1">{{ number_format($res['imported']) }}</p>
                    </div>

                    <!-- Failed Rows -->
                    <div class="bg-white p-4 rounded-2xl border border-rose-200/80 shadow-xs bg-rose-50/20">
                        <p class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Failed Rows</p>
                        <p class="text-2xl font-bold text-rose-700 mt-1">{{ number_format($res['failed']) }}</p>
                    </div>

                    <!-- Duplicate Rows -->
                    <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-xs bg-amber-50/20">
                        <p class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Duplicates</p>
                        <p class="text-2xl font-bold text-amber-700 mt-1">{{ number_format($res['duplicates']) }}</p>
                    </div>
                </div>

                @if(!empty($res['errors']))
                    <div class="bg-white border border-rose-200/80 rounded-2xl p-5 shadow-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-rose-100">
                            <div>
                                <h4 class="text-sm font-bold text-rose-700">Validation & Error Details</h4>
                                <p class="text-xs text-slate-500">The following row issues were detected during file parsing.</p>
                            </div>
                            @if(session('import_failed_rows'))
                                <a href="{{ route('import.errors') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-semibold border border-rose-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Download Error Report (.csv)</span>
                                </a>
                            @endif
                        </div>

                        <div class="mt-3 max-h-60 overflow-y-auto divide-y divide-slate-100 text-xs">
                            @foreach($res['errors'] as $err)
                                <div class="py-2.5 flex items-start justify-between gap-3">
                                    <div>
                                        <span class="font-bold text-slate-800">Row {{ $err['row'] }}:</span>
                                        <span class="text-slate-600 ml-1">{{ $err['name'] }}</span>
                                    </div>
                                    <span class="text-rose-600 font-medium shrink-0">{{ $err['error'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Drag & Drop Upload Form -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
            <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" id="upload-form">
                @csrf

                <!-- Dropzone Area -->
                <div id="dropzone" class="border-2 border-dashed border-slate-200 hover:border-indigo-500 rounded-2xl p-8 sm:p-12 text-center transition cursor-pointer bg-slate-50/50 hover:bg-indigo-50/20">
                    <input type="file" name="file" id="file-input" class="hidden" accept=".xlsx,.csv,.txt" required>

                    <div class="mx-auto w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>

                    <h3 class="text-base font-bold text-slate-900" id="file-name-display">
                        Drop your Excel or CSV file here, or <span class="text-indigo-600 underline">browse</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 max-w-sm mx-auto">
                        Supports Microsoft Excel (.xlsx) and Comma-Separated Values (.csv) up to 10MB.
                    </p>

                    <div class="mt-4">
                        <button type="button" onclick="document.getElementById('file-input').click()" class="inline-flex items-center px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition">
                            Select File
                        </button>
                    </div>
                </div>

                @error('file')
                    <p class="text-xs text-rose-600 mt-2">{{ $message }}</p>
                @enderror

                <!-- Expected Schema Instructions -->
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Required Spreadsheet Columns</h4>
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-xs text-slate-700 overflow-x-auto">
                        party_name, mobile_number, map_link, latitude, longitude, party_type, priority_status
                    </div>

                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-slate-500">
                        <div>&bull; <strong class="text-slate-700">party_name:</strong> Required. Name of the client party.</div>
                        <div>&bull; <strong class="text-slate-700">latitude & longitude:</strong> Decimals (e.g. 12.971598, 77.594562).</div>
                        <div>&bull; <strong class="text-slate-700">party_type:</strong> Accepts <code class="text-indigo-600">new</code> or <code class="text-indigo-600">old</code>. Default is new.</div>
                        <div>&bull; <strong class="text-slate-700">priority_status:</strong> Accepts <code class="text-emerald-600">priority</code> or <code class="text-slate-600">non_priority</code>.</div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" id="submit-btn" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs shadow-indigo-600/20 transition flex items-center gap-2">
                        <span>Upload & Import</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('file-input');
            const fileNameDisplay = document.getElementById('file-name-display');

            dropzone.addEventListener('click', () => fileInput.click());

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('border-indigo-500', 'bg-indigo-50/30');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('border-indigo-500', 'bg-indigo-50/30');
                });
            });

            dropzone.addEventListener('drop', (e) => {
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    updateDisplay(fileInput.files[0]);
                }
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    updateDisplay(fileInput.files[0]);
                }
            });

            function updateDisplay(file) {
                const sizeKb = (file.size / 1024).toFixed(1);
                fileNameDisplay.innerHTML = `Selected: <span class="text-indigo-600 font-semibold">${file.name}</span> (${sizeKb} KB)`;
            }
        });
    </script>
    @endpush
</x-layouts.app>
