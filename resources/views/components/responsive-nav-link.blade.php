@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg border-l-4 border-indigo-500 bg-indigo-50 ps-3 pe-4 py-2 text-start text-base font-semibold text-indigo-700 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full rounded-lg border-l-4 border-transparent ps-3 pe-4 py-2 text-start text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
