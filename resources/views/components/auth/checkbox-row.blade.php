{{--
    Checkbox Row Component
    Usage:
        <x-auth.checkbox-row
            id="terms"
            wrapId="termsWrap"
            iconId="termsIcon"
            :checked="false"
        >
            I agree to the <a href="#" class="text-[#9B7DFF] hover:text-white transition-colors">Terms</a>
        </x-auth.checkbox-row>
--}}

@props([
    'id' => 'checkbox',
    'wrapId'  => '',
    'iconId'  => '',
    'checked' => false,
    'error' => ''
])
<div class="flex flex-col gap-1">
    <label
        id="{{ $wrapId ?: $id . 'Wrap' }}"
        class="flex items-start gap-2.5 cursor-pointer group"
    >
        <input type="checkbox" id="{{ $id }}" {{ $attributes->whereStartsWith('wire:') }} class="sr-only peer" />

        {{-- Custom box --}}
        <div id="{{ $wrapId ?: $id . 'Wrap' }}_box"
            class="w-[17px] h-[17px] shrink-0 mt-[1px] rounded-[5px] border
                    flex items-center justify-center transition-all duration-150
                    border-white/[.15] bg-transparent
                    peer-checked:bg-[#9B7DFF] peer-checked:border-[#9B7DFF]
                    group-hover:border-[rgba(155,125,255,.4)]">

            <svg id="{{ $iconId ?: $id . 'Icon' }}"
                width="9" height="9" fill="none" viewBox="0 0 9 9"
                stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                class="opacity-0 peer-checked:opacity-100 transition-opacity duration-150">
                <path d="M1 4.5L3.5 7L8 2"/>
            </svg>
        </div>

        <span class="text-[12.5px] text-[#8888aa] leading-snug group-hover:text-[#c8c8e0] transition-colors">{{ $slot }}</span>
    </label>

    @if($error && $errors->has($error))
        <p class="text-[11.5px] text-red-400 ml-[29px] flex items-center gap-1">
            <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="currentColor" stroke-width="1.5">
                <circle cx="5.5" cy="5.5" r="4.5"/>
                <path d="M5.5 3.5v2.5M5.5 7.5v.2" stroke-linecap="round"/>
            </svg>
            {{ $errors->first($error) }}
        </p>
    @endif
</div>