@props([
    'image' => null,
    'widths' => [400, 800, 1200],
    'sizes' => '(min-width: 768px) 50vw, 100vw',
    'alt' => null,
])

@if ($image)
    @php
        $srcset = collect($widths)
            ->map(fn (int $width) => $image->transformed($width).' '.$width.'w')
            ->implode(', ');
    @endphp
    <img
        src="{{ $image->transformed(800) }}"
        srcset="{{ $srcset }}"
        sizes="{{ $sizes }}"
        alt="{{ $alt ?? $image->alt }}"
        loading="lazy"
        {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}
    >
@else
    <div {{ $attributes->merge(['class' => 'flex aspect-square items-center justify-center bg-hempe-light text-hempe']) }}>
        HEMPE
    </div>
@endif
