{{-- resources/views/livewire/terminal/index.blade.php --}}
<div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-6">
    <div>
        <h2 class="text-lg font-semibold text-white mb-4">Live Formation Feed</h2>
        <livewire:terminal.formation-feed />
    </div>

    <div class="bg-[#0d1120] border border-white/5 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-400 mb-4">LIVE ACTIVITY</h3>
        <livewire:terminal.activity-ticker />
    </div>
</div>