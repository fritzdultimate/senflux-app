{{--
    Form Input Component
    Usage:
        <x-auth.form-input
            id="email"
            label="Email address"
            type="email"
            placeholder="you@example.com"
            autocomplete="email"
        >
            <x-slot:icon> ... svg ... </x-slot:icon>
            <x-slot:suffix> ... toggle button ... </x-slot:suffix>
        </x-auth.form-input>
--}}

@props([
    'id'           => '',
    'label'        => '',
    'labelNote'    => '',    // e.g. "(optional)"
    'type'         => 'text',
    'placeholder'  => '',
    'autocomplete' => 'off',
    'extraInputAttrs' => '',  // raw attribute string for things like oninput, style
    'error' => '',
    'model' => ''
])

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $id }}" class="text-[11.5px] font-semibold tracking-[.07em] uppercase text-[#6b6b8a]">
            {{ $label }}
            @if($labelNote)
                <span class="text-[#4a4a6a] font-normal normal-case tracking-normal">{{ $labelNote }}</span>
            @endif
        </label>
    @endif

    <div class="relative flex items-center">
        {{-- Leading icon slot --}}
        @isset($icon)
            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#4a4a6a]">
                {{ $icon }}
            </div>
        @endisset

        <input
            id="{{ $id }}"
            name="{{ $id }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            @if ($model === 'password')
                wire:model.live="{{ $model }}"
            @else
                wire:model.blur="{{ $model }}"
            @endif
            {{ $attributes->merge([
                'class' => 'w-full bg-[rgba(255,255,255,.04)] border border-[rgba(255,255,255,.07)] rounded-xl
                            text-sm text-white placeholder-[#4a4a6a]
                            py-2.5 pr-10
                            ' . (isset($icon) ? 'pl-10' : 'pl-3.5') . '
                            focus:outline-none focus:border-[rgba(155,125,255,.5)] focus:bg-[rgba(155,125,255,.06)]
                            transition-all duration-200'
            ]) }}
        />

        {{-- Trailing suffix slot (e.g. eye toggle) --}}
        @isset($suffix)
            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-[#4a4a6a] hover:text-[#c8c8e0] cursor-pointer transition-colors">
                {{ $suffix }}
            </div>
        @endisset
    </div>

    {{-- Extra slot for strength meter, hints, errors --}}
    @isset($below)
        {{ $below }}
    @endisset

    {{-- Inline error --}}
    @if($error && $errors->has($error))
        <p class="text-[11.5px] text-red-400 flex items-center gap-1">
            <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="currentColor" stroke-width="1.5">
                <circle cx="5.5" cy="5.5" r="4.5"/>
                <path d="M5.5 3.5v2.5M5.5 7.5v.2" stroke-linecap="round"/>
            </svg>
            {{ $errors->first($error) }}
        </p>
    @endif
</div>