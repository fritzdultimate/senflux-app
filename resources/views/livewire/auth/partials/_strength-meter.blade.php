{{-- partials/_strength-meter.blade.php --}}
{{-- Reads $password from Livewire component scope --}}

@php
    $len      = strlen($password ?? '');
    $hasNum   = preg_match('/\d/', $password ?? '');
    $hasSym   = preg_match('/[\W_]/', $password ?? '');
    $hasUpper = preg_match('/[A-Z]/', $password ?? '');

    $score = 0;
    if ($len >= 8)   $score++;
    if ($len >= 12)  $score++;
    if ($hasNum)     $score++;
    if ($hasSym)     $score++;
    if ($hasUpper)   $score++;

    // Clamp to 4 bars
    $filled = match(true) {
        $score === 0                => 0,
        $score <= 1                 => 1,
        $score <= 2                 => 2,
        $score <= 3                 => 3,
        default                     => 4,
    };

    $label = match($filled) {
        0 => ['text' => 'At least 8 characters, one number, one special character', 'color' => 'text-[#4a4a6a]'],
        1 => ['text' => 'Too weak',   'color' => 'text-red-400'],
        2 => ['text' => 'Weak',       'color' => 'text-amber-400'],
        3 => ['text' => 'Good',       'color' => 'text-sky-400'],
        4 => ['text' => 'Strong ✓',   'color' => 'text-emerald-400'],
    };

    $barColors = [
        1 => 'bg-red-500',
        2 => 'bg-amber-400',
        3 => 'bg-sky-400',
        4 => 'bg-emerald-400',
    ];
@endphp

<div class="mt-2">
    <div class="flex gap-1 mb-1.5">
        @for($i = 1; $i <= 4; $i++)
            <div class="flex-1 h-[3px] bg-white/[.07] rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-300
                            {{ $i <= $filled ? ($barColors[$filled] ?? 'bg-emerald-400') : '' }}"
                     style="width: {{ $i <= $filled ? '100%' : '0' }}">
                </div>
            </div>
        @endfor
    </div>
    <p class="text-[11.5px] transition-colors duration-200 {{ $label['color'] }}">
        {{ $label['text'] }}
    </p>
</div>