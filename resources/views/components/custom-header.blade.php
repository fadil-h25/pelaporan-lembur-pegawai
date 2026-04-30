@props(['title' => '', 'subtitle' => ''])

<x-header :title="$title" :subtitle="$subtitle" >
    <x-slot:actions>
        <x-dropdown right>
            <x-slot:trigger>
                <x-avatar 
                    :placeholder="collect(explode(' ', auth()->user()->name))->map(fn($n) => $n[0])->take(2)->implode('')" 
                    :title="auth()->user()->name" 
                    :subtitle="auth()->user()->email" 
                    class="!w-10 cursor-pointer hover:opacity-80 transition-all" 
                />
            </x-slot:trigger>
            
            <x-menu-item title="Profile" icon="o-user" link="/profile" />
            <x-menu-item title="Logout" icon="o-power" link="/logout" no-wire-navigate class="text-error" />
        </x-dropdown>
    </x-slot:actions>
</x-header>