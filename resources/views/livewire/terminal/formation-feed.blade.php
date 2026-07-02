{{-- resources/views/livewire/terminal/formation-feed.blade.php --}}
<div wire:poll.15s>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach ($formations as $formation)
            <x-formation-card :formation="$formation" />
        @endforeach
    </div>

    @if ($activeFormationId)
        <livewire:terminal.formation-detail :formation-id="$activeFormationId" :key="$activeFormationId" />
    @endif
</div>