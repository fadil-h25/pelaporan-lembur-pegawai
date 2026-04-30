@props(['title' => '', 'subtitle' => ''])

<x-header :title="$title" :subtitle="$subtitle" separator progress-indicator>
    <x-slot:actions>
        {{ $slot }}
    </x-slot:actions>
</x-header>
