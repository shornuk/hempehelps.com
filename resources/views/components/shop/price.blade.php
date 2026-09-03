@props(['money', 'compare' => null])

<span {{ $attributes->class('font-medium') }}>
    {{ $money->formatted() }}
    @if ($compare && $compare->amount !== $money->amount)
        <span class="ml-2 text-sm text-muted line-through">{{ $compare->formatted() }}</span>
    @endif
</span>
