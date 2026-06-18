{{--
    SIDEBAR COMPONENT

    @author
    @version     1.0
    @since       2026-06-18

    Zijbalk navigatie voor de applicatie. Toont verschillende menu-items
    op basis van de gebruikersrol (technieker, magazijn, admin).
    Ondersteunt zowel desktop (sticky) als mobile (fullscreen) weergave.
--}}

@php
    $user = Auth::user();
    $role = $user->role;
    $isMobile = $mobile ?? false;
    $homeRoute = match ($role) {
        'admin' => route('materials.index'),
        'magazijn' => route('magazijn.materials.index'),
        'technieker' => route('technician.materials.index'),
        default => route('login'),
    };
@endphp

<div class="relative flex w-72 flex-col overflow-hidden text-white shadow-2xl {{ $isMobile ? 'h-full' : 'h-screen sticky top-0' }}">

    {{-- Background --}}
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/sidebar-bg.jpg') }}"
            alt="Aquafin"
            class="h-full w-full object-cover">
    </div>

    {{-- Overlay --}}
    <div
        class="absolute inset-0 bg-gradient-to-b from-[#0F4C81]/70 via-[#1E6BA8]/50 to-[#0F4C81]/80">
    </div>

    <div class="relative z-10 flex h-full flex-col">

        {{-- Logo + close button on mobile --}}
        <div class="border-b border-white/20 p-6 backdrop-blur-sm">
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-1 justify-center">
                    <a
                        href="{{ $homeRoute }}"
                        @click="mobileSidebarOpen = false"
                        class="transition hover:opacity-90"
                    >
                        <img
                            src="{{ asset('images/aquafin-logo.png') }}"
                            alt="Aquafin"
                            class="w-44 object-contain">
                    </a>
                </div>

                @if($isMobile)
                    <button
                        type="button"
                        @click="mobileSidebarOpen = false"
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-white hover:bg-white/20"
                        title="Menu sluiten"
                        aria-label="Menu sluiten"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             class="h-6 w-6">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 overflow-y-auto px-4 py-6">

            <ul class="space-y-3">

                {{-- TECHNIEKER MENU --}}
                @if($role == 'technieker')

                    <li>
                        <a href="{{ route('technician.materials.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Materialen</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('orders.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Bestellingen</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('tickets.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Support</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('flood-risk.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Overstromingsrisico</span>
                        </a>
                    </li>

                @endif

                {{-- MAGAZIJN MENU --}}
                @if($role == 'magazijn')

                    <li>
                        <a href="{{ route('magazijn.orders.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Bestellingen</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('magazijn.materials.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Voorraad</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('tickets.warehouse.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Support aanvragen</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('flood-risk.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Overstromingsrisico</span>
                        </a>
                    </li>

                @endif

                {{-- ADMIN MENU --}}
                @if($role == 'admin')

                    <li>
                        <a href="{{ route('admin.users.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Gebruikers</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('materials.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Materialen</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.orders.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Bestellingen</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.flood-risk.index') }}"
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15">
                            <span>Overstromingsrisico</span>
                        </a>
                    </li>

                @endif

            </ul>

        </nav>

        {{-- Mobile bottom actions --}}
        @if($isMobile)
            <div class="border-t border-white/20 p-4 backdrop-blur-sm">
                <a
                    href="{{ route('profile.edit') }}"
                    @click="mobileSidebarOpen = false"
                    class="mb-3 flex items-center gap-3 rounded-xl px-4 py-3 font-bold transition-all hover:bg-white/15"
                >
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 font-bold uppercase text-white">
                        {{ substr($user->name, 0, 1) }}
                    </div>

                    <div class="min-w-0">
                        <p class="truncate">
                            {{ explode(' ', $user->name)[0] }}
                        </p>

                        <p class="text-xs font-normal text-white/80">
                            Profiel bekijken
                        </p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left font-bold transition-all hover:bg-white/15"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="1.8"
                             stroke="currentColor"
                             class="h-6 w-6">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m-3-3h9m0 0l-3-3m3 3l-3 3"/>
                        </svg>

                        <span>Uitloggen</span>
                    </button>
                </form>
            </div>
        @endif

    </div>

</div>


