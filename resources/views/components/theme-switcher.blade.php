@props(['mobile' => false])

@if($mobile)
<div class="px-3 py-3 rounded-2xl border-slate-200/80 bg-slate-50/60 transition" id="mobile-theme-box">
    <div class="flex items-center justify-between mb-2.5">
        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Theme</span>
    </div>
    <div class="grid grid-cols-3 gap-2">
        <!-- Classic Default -->
        <button type="button" onclick="setAppTheme('default')" data-theme-btn="default" class="theme-choice-btn flex flex-col items-center gap-1.5 p-2 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-700 hover:border-indigo-400 transition">
            <span class="w-6 h-6 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
            </span>
            <span class="text-[11px] font-semibold">Classic</span>
        </button>

        <!-- Black Iris & Frozen -->
        <button type="button" onclick="setAppTheme('black-iris')" data-theme-btn="black-iris" class="theme-choice-btn flex flex-col items-center gap-1.5 p-2 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-700 hover:border-[#A0BDDB] transition">
            <span class="w-6 h-6 rounded-full bg-[#080813] border border-[#A0BDDB]/40 flex items-center justify-center">
                <span class="w-2.5 h-2.5 rounded-full bg-[#A0BDDB]"></span>
            </span>
            <span class="text-[11px] font-semibold">Black Iris</span>
        </button>

        <!-- Shadow & Citrus -->
        <button type="button" onclick="setAppTheme('shadow-citrus')" data-theme-btn="shadow-citrus" class="theme-choice-btn flex flex-col items-center gap-1.5 p-2 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-700 hover:border-[#E4FD97] transition">
            <span class="w-6 h-6 rounded-full bg-[#141414] border border-[#E4FD97]/40 flex items-center justify-center">
                <span class="w-2.5 h-2.5 rounded-full bg-[#E4FD97]"></span>
            </span>
            <span class="text-[11px] font-semibold">Citrus</span>
        </button>
    </div>
</div>
@else
<div class="relative inline-block text-left" id="theme-dropdown-container">
    <button type="button" id="theme-menu-btn" onclick="toggleThemeMenu(event)" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-medium border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition hover:border-slate-300">
        <span id="theme-current-swatch" class="flex items-center justify-center w-3.5 h-3.5 rounded-full border border-slate-300 overflow-hidden shrink-0">
            <span class="w-full h-full bg-indigo-600"></span>
        </span>
        <span id="theme-current-label" class="font-semibold text-slate-800">Theme</span>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div id="theme-menu-dropdown" class="hidden absolute right-0 mt-2 w-56 rounded-2xl bg-white border border-slate-200 z-50 p-2 space-y-1">
        <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Select Theme</div>
        
        <!-- Classic Default -->
        <button type="button" onclick="setAppTheme('default')" data-theme-option="default" class="theme-option-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left text-xs font-medium text-slate-700 hover:bg-slate-50 transition border border-transparent">
            <span class="w-5 h-5 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
            </span>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-slate-900 leading-tight">Classic Light</div>
            </div>
            <span class="theme-check hidden text-indigo-600 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </span>
        </button>

        <!-- Black Iris & Frozen -->
        <button type="button" onclick="setAppTheme('black-iris')" data-theme-option="black-iris" class="theme-option-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left text-xs font-medium text-slate-700 hover:bg-slate-50 transition border border-transparent">
            <span class="w-5 h-5 rounded-full bg-[#080813] border border-[#A0BDDB]/50 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-[#A0BDDB]"></span>
            </span>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-slate-900 leading-tight">Black Iris &amp; Frozen</div>
            </div>
            <span class="theme-check hidden text-[#A0BDDB] shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </span>
        </button>

        <!-- Shadow & Citrus -->
        <button type="button" onclick="setAppTheme('shadow-citrus')" data-theme-option="shadow-citrus" class="theme-option-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left text-xs font-medium text-slate-700 hover:bg-slate-50 transition border border-transparent">
            <span class="w-5 h-5 rounded-full bg-[#141414] border border-[#E4FD97]/50 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-[#E4FD97]"></span>
            </span>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-slate-900 leading-tight">Shadow &amp; Citrus</div>
            </div>
            <span class="theme-check hidden text-[#E4FD97] shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </span>
        </button>
    </div>
</div>
@endif
