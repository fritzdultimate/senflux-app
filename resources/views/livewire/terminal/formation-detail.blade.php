{{-- resources/views/livewire/terminal/formation-detail.blade.php --}}
<div class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4" wire:click.self="$parent.closeFormation">
    <div class="bg-[#0d1120] border border-white/10 rounded-2xl max-w-2xl w-full p-8 max-h-[90vh] overflow-y-auto">

        <x-formation-card :formation="$formation" :readonly="true" />

        <div class="mt-8">
            <h4 class="text-sm font-semibold text-gray-400 mb-3">TIMELINE</h4>
            <div class="space-y-2 border-l border-white/10 pl-4">
                @foreach ($timeline as $event)
                    <div>
                        <p class="text-sm text-gray-300">{{ $event->message }}</p>
                        <p class="text-xs text-gray-600">{{ $event->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            <h4 class="text-sm font-semibold text-gray-400 mb-3">DEPLOYMENT</h4>
            @if ($deployment['status'] === 'already_deployed')
                <p class="text-green-400 text-sm">✓ Already Deployed — Slot #{{ $deployment['slot']->slot_number }}</p>
            @elseif ($deployment['status'] === 'eligible_for_deployment')
                <p class="text-[#9B7DFF] text-sm mb-3">Eligible For Deployment</p>
                <button wire:click="deploy({{ $deployment['slot']->id }}, {{ $formation->id }})"
                        class="text-sm bg-[#9B7DFF] text-white rounded-lg px-4 py-2">
                    Deploy Slot #{{ $deployment['slot']->slot_number }}
                </button>
            @else
                <p class="text-gray-500 text-sm">Waiting For Qualification</p>
            @endif
        </div>

        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('formations.share', $formation) }}" target="_blank" class="text-sm text-[#9B7DFF]">
                Share this formation ↗
            </a>
            <button wire:click="$parent.closeFormation" class="text-sm text-gray-500">Close</button>
        </div>
    </div>
</div>