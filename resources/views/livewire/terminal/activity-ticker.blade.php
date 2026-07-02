{{-- resources/views/livewire/terminal/activity-ticker.blade.php --}}
<div wire:poll.10s class="space-y-3">
    @foreach ($events as $event)
        <div class="text-sm flex items-start gap-2">
            <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" style="background: {{ $event->formation->state->color() }}"></span>
            <div>
                <p class="text-gray-300">{{ $event->message }}</p>
                <p class="text-xs text-gray-600">{{ $event->created_at->diffForHumans() }}</p>
            </div>
        </div>
    @endforeach
</div>