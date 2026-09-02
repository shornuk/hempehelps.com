@props(['rating' => 0])

<span {{ $attributes->class('inline-flex gap-0.5 text-hempe') }} aria-label="{{ $rating }} out of 5">
    @for ($i = 1; $i <= 5; $i++)
        <span>{{ $i <= round($rating) ? '★' : '☆' }}</span>
    @endfor
</span>
