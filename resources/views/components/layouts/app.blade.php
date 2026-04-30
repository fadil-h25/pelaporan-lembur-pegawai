<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="min-h-screen font-sans antialiased bg-base-100">

        {{-- NAVBAR mobile only --}}
        <x-nav sticky class="lg:hidden">
            <x-slot:brand>
                <x-app-brand />
            </x-slot:brand>
            <x-slot:actions>
                <label for="main-drawer" class="lg:hidden me-3">
                    <x-icon name="o-bars-3" class="cursor-pointer" />
                </label>
            </x-slot:actions>
        </x-nav>

        {{-- MAIN --}}
        <x-main>
            {{-- SIDEBAR --}}
            <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

                {{-- BRAND --}}
                <x-app-brand class="px-5 pt-4" />

                {{-- MENU --}}
                <x-menu activate-by-route>

                    <div class="mt-2">
                        <x-menu-item title="Dashboard" icon="o-chart-bar" link="/dashboard" />
                        <x-menu-item title="Dokumen Lembur" icon="o-document-text" link="/lembur" />
                        
                        @if(auth()->user() && (auth()->user()->role === \App\UserRole::ADMIN->value || auth()->user()->role === 'admin'))
                            <x-menu-item title="Manajemen User" icon="o-users" link="/management-user" />
                        @endif
                        {{-- Garis pemisah supaya menu utama gak nyampur sama menu logout --}}
<x-menu-separator />

<x-menu-item 
    title="Logout" 
    icon="o-power" 
    class="text-error-content bg-error/10 hover:bg-error/20" 
   
    no-wire-navigate 
    link="/logout"
/>
                    </div>
                </x-menu>
            </x-slot:sidebar>

            {{-- The `$slot` goes here --}}
            <x-slot:content class="!p-0"> {{-- Hilangkan padding bawaan slot jika perlu --}}
                <div class="bg-base-200 min-h-screen p-5">
                    {{ $slot }}
                </div>
            </x-slot:content>

        </x-main>

        {{--  TOAST area --}}
        <x-toast />
    </body>

</html>
