{{--
    Strength Meter Component
    Renders the 4-bar strength indicator driven by checkStrength() JS.
    Usage: <x-auth.strength-meter />
--}}

<div class="mt-2">
    {{-- Bars --}}
    <div class="flex gap-1 mb-1.5">
        @foreach(['s1' => 'var(--sf-red,#ef4444)', 's2' => 'var(--sf-yellow,#f59e0b)', 's3' => 'var(--sf-green,#10B981)', 's4' => 'var(--sf-green,#10B981)'] as $id => $color)
            <div class="flex-1 h-[3px] bg-white/[.07] rounded-full overflow-hidden">
                <div id="{{ $id }}"
                     class="strength-bar h-full w-0 rounded-full transition-all duration-300"
                     style="background:{{ $color }}">
                </div>
            </div>
        @endforeach
    </div>

    {{-- Label --}}
    <p id="strengthLabel" class="text-[11.5px] text-[#4a4a6a]">
        At least 8 characters, one number, one special character
    </p>
</div>