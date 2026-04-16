@props([
    'variant' => 'body',
    'as' => null,
])

@php
$variants = [
    'title' => 'text-2xl font-bold text-gray-900 dark:text-white',
    'subtitle' => 'text-lg font-bold text-gray-900 dark:text-white',
    'heading' => 'text-lg font-semibold text-gray-900 dark:text-white',
    'label' => 'block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400',
    'body' => 'text-sm text-gray-900 dark:text-white',
    'muted' => 'text-sm text-gray-500 dark:text-gray-400',
];

$classes = $variants[$variant] ?? $variants['body'];

if (! $as) {
    if ($variant === 'title') {
        $as = 'h1';
    } elseif (in_array($variant, ['subtitle', 'heading'])) {
        $as = 'h2';
    } elseif ($variant === 'label') {
        $as = 'label';
    } else {
        $as = 'p';
    }
}
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $as }}>
