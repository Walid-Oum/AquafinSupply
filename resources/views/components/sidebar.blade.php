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
    $mobile = $mobile ?? false;
    $user = Auth::user();

    $homeRoute = match ($user?->role) {
        'admin' => route('materials.index'),
        'magazijn' => route('magazijn.orders.index'),
        'technieker' => route('technician.materials.index'),
        default => route('login'),
    };

    $linkBaseClasses = 'flex items-center rounded-xl px-5 py-3 text-base font-bold transition';

    $activeClasses = 'bg-white/20 text-white shadow-sm';
    $inactiveClasses = 'text-white hover:bg-white/10';

    $navLinkClasses = function (bool $active) use ($linkBaseClasses, $activeClasses, $inactiveClasses) {
        return $linkBaseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
    };
@endphp

<aside
    class="flex h-screen w-72 flex-col overflow-y-auto bg-[#0F4C81] bg-cover bg-center text-white shadow-xl lg:sticky lg:top-0"
    style="background-image: linear-gradient(rgba(15, 76, 129, 0.88), rgba(15, 76, 129, 0.88)), url('{{ asset('images/sidebar-bg.jpg') }}');"
>
    <div class="flex min-h-full flex-col">
        <div class="border-b border-white/20 px-6 py-8">
            <div class="flex items-center justify-between">
                <a
                    href="{{ $homeRoute }}"
                    class="block transition hover:scale-[1.02] hover:opacity-90"
                    title="Naar startpagina"
                    @click="mobileSidebarOpen = false"
                >
                    <img
                        src="{{ asset('images/aquafin-logo.png') }}"
                        alt="Aquafin"
                        class="mx-auto h-28 w-auto object-contain"
                    >
                </a>

                @if($mobile)
                    <button
                        type="button"
                        class="rounded-xl bg-white/10 p-2 text-white transition hover:bg-white/20 lg:hidden"
                        @click="mobileSidebarOpen = false"
                        aria-label="Menu sluiten"
                    >
                        ✕
                    </button>
                @endif
            </div>
        </div>

        <nav class="flex-1 space-y-3 px-4 py-8">
            @if($user?->role === 'admin')
                <a
                    href="{{ route('admin.users.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('admin.users.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Gebruikers
                </a>

                <a
                    href="{{ route('materials.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('materials.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Materialen
                </a>

<<<<<<< HEAD
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('admin.orders.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Bestellingen
                </a>
=======
                {{-- TECHNIEKER MENU --}}
                @if($role == 'technieker')
>>>>>>> Doc/warehouse

                <a
                    href="{{ route('admin.flood-risk.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('admin.flood-risk.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Overstromingsrisico
                </a>
            @elseif($user?->role === 'magazijn')
                <a
                    href="{{ route('magazijn.orders.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('magazijn.orders.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Bestellingen
                </a>

                <a
                    href="{{ route('magazijn.materials.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('magazijn.materials.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Voorraad
                </a>

                <a
                    href="{{ route('tickets.warehouse.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('tickets.warehouse.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Support aanvragen
                </a>

                <a
                    href="{{ route('flood-risk.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('flood-risk.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Overstromingsrisico
                </a>
            @elseif($user?->role === 'technieker')
                <a
                    href="{{ route('technician.materials.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('technician.materials.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Materialen
                </a>

                <a
                    href="{{ route('orders.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('orders.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Bestellingen
                </a>

<<<<<<< HEAD
                <a
                    href="{{ route('tickets.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('tickets.index') || request()->routeIs('tickets.create')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Support
                </a>
=======
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
>>>>>>> Doc/warehouse

                <a
                    href="{{ route('flood-risk.index') }}"
                    class="{{ $navLinkClasses(request()->routeIs('flood-risk.*')) }}"
                    @click="mobileSidebarOpen = false"
                >
                    Overstromingsrisico
                </a>
            @endif
        </nav>

        @if($mobile)
            <div class="border-t border-white/20 px-4 py-5">
                <a
                    href="{{ route('profile.edit') }}"
                    class="mb-3 flex items-center gap-3 rounded-2xl bg-white/10 p-3 transition hover:bg-white/20"
                    @click="mobileSidebarOpen = false"
                >
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white font-bold text-[#0F4C81]">
                        {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                    </div>

                    <div class="min-w-0">
                        <p class="truncate font-bold text-white">
                            {{ $user?->name }}
                        </p>

                        <p class="text-sm capitalize text-white/70">
                            {{ $user?->role }}
                        </p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-2xl bg-white px-5 py-3 font-bold text-[#0F4C81] transition hover:bg-blue-50"
                    >
                        Uitloggen
                    </button>
                </form>
            </div>
        @endif
    </div>
</aside>

<<<<<<< HEAD
=======
</div>
>>>>>>> Doc/warehouse
