{{--
    NAVBAR COMPONENT
    Navigatiebalk met logo, winkelmandje, notificaties en profiel.
    Toont verschillende elementen op basis van gebruikersrol.
    @author 
    @version 1.0
--}}

@php
    $user = Auth::user();

    $cartCount = count(session('cart', []));

    $canSeeNotifications = in_array($user->role, ['technieker', 'magazijn']);

    $unreadNotificationCount = $canSeeNotifications
        ? $user->userNotifications()->whereNull('read_at')->count()
        : 0;

    $latestNotifications = $canSeeNotifications
        ? $user->userNotifications()->latest()->take(5)->get()
        : collect();

    $homeRoute = match ($user->role) {
        'technieker' => route('technician.materials.index'),
        'magazijn' => route('magazijn.orders.index'),
        'admin' => route('admin.users.index'),
        default => '#',
    };
@endphp

<div class="sticky top-0 z-30 flex h-14 items-center border-b bg-white px-4 shadow-md sm:h-16 sm:px-6 lg:h-20 lg:px-8">

    {{-- Mobile: hamburger + logo --}}
    <div class="flex items-center gap-3 lg:hidden">
        <button
            type="button"
            @click="mobileSidebarOpen = true"
            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-[#0F4C81] hover:bg-gray-50"
            title="Menu openen"
            aria-label="Menu openen"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="2"
                 stroke="currentColor"
                 class="h-7 w-7">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <a href="{{ $homeRoute }}" class="flex items-center">
            <img
                src="{{ asset('images/aquafin-logo.png') }}"
                alt="Aquafin"
                class="h-8 w-auto object-contain sm:h-9"
            >
        </a>
    </div>

    {{-- Right actions --}}
    <div class="ml-auto flex items-center gap-3 sm:gap-5 lg:gap-6">

        {{-- Winkelmandje (enkel voor technieker) --}}
        @if($user->role === 'technieker')
            <a
                href="{{ route('cart.index') }}"
                class="relative transition hover:scale-110"
                title="Winkelmandje"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.8"
                     stroke="#0F4C81"
                     class="h-7 w-7 sm:h-8 sm:w-8 lg:h-10 lg:w-10">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M2.25 3h1.386a1.5 1.5 0 011.464 1.175L5.383 6.75m0 0h13.867l-1.2 6H6.383m-1-6L6.75 15m0 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm10.5 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"/>
                </svg>

                <span
                    id="cart-count"
                    class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white {{ $cartCount > 0 ? '' : 'hidden' }}"
                >
                    {{ $cartCount }}
                </span>
            </a>
        @endif

        {{-- Notificaties (enkel voor technieker en magazijn) --}}
        @if($canSeeNotifications)
            <div
                x-data="{
                    open: false,

                    toggleNotifications() {
                        this.open = !this.open;

                        if (this.open) {
                            fetch('{{ route('notifications.markAsRead') }}', {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });

                            const badge = document.getElementById('notification-count');

                            if (badge) {
                                badge.classList.add('hidden');
                            }
                        }
                    }
                }"
                class="relative"
            >
                <button
                    type="button"
                    @click="toggleNotifications()"
                    class="relative transition hover:scale-110"
                    title="Notificaties"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.8"
                         stroke="#0F4C81"
                         class="h-7 w-7 sm:h-8 sm:w-8 lg:h-10 lg:w-10">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M14.857 17.082a2.25 2.25 0 01-4.714 0m8.607-2.332A3.375 3.375 0 0117.25 12V9.75a5.25 5.25 0 00-10.5 0V12a3.375 3.375 0 01-1.5 2.75l-.33.22a.75.75 0 00.416 1.38h13.328a.75.75 0 00.416-1.38l-.33-.22z"/>
                    </svg>

                    <span
                        id="notification-count"
                        class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-xs font-bold text-white {{ $unreadNotificationCount > 0 ? '' : 'hidden' }}"
                    >
                        {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                    </span>
                </button>

                {{-- Dropdown notificaties --}}
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    x-cloak
                    class="absolute right-0 z-50 mt-3 w-[calc(100vw-2rem)] max-w-sm rounded-xl border border-gray-200 bg-white shadow-xl sm:w-96"
                >
                    <div class="border-b px-4 py-3">
                        <h3 class="font-bold text-gray-900">
                            Notificaties
                        </h3>

                        <p class="text-sm text-gray-500">
                            Laatste updates over bestellingen en supportaanvragen.
                        </p>
                    </div>

                    <div class="max-h-80 overflow-y-auto">
                        @forelse($latestNotifications as $notification)
                            <a
                                href="{{ $notification->link ?? '#' }}"
                                class="block border-b px-4 py-3 hover:bg-gray-50"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900">
                                            {{ $notification->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $notification->message }}
                                        </p>

                                        <p class="mt-2 text-xs text-gray-400">
                                            {{ $notification->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>

                                    @if(! $notification->is_read)
                                        <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">
                                            Nieuw
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-sm text-gray-500">
                                Je hebt nog geen notificaties.
                            </div>
                        @endforelse
                    </div>

                    <div class="px-4 py-3">
                        <a
                            href="{{ route('notifications.index') }}"
                            class="block text-center text-sm font-semibold text-[#0F4C81] hover:underline"
                        >
                            Bekijk alle notificaties
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Desktop/tablet profile --}}
        <a href="{{ route('profile.edit') }}"
           class="hidden items-center gap-2 transition hover:opacity-80 sm:flex sm:gap-3">

            <div
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0F4C81] font-bold uppercase text-white sm:h-10 sm:w-10">

                {{ substr($user->name, 0, 1) }}
            </div>

            <span class="hidden font-medium text-gray-700 md:inline">
                {{ explode(' ', $user->name)[0] }}
            </span>
        </a>

        <div class="hidden h-10 w-px bg-gray-300 sm:block"></div>

        {{-- Desktop/tablet logout --}}
        <form
            method="POST"
            action="{{ route('logout') }}"
            class="hidden items-center sm:flex">

            @csrf

            <button
                type="submit"
                class="transition hover:scale-110"
                title="Uitloggen">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="#0F4C81"
                    class="h-8 w-8 sm:h-9 sm:w-9 lg:h-10 lg:w-10">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m-3-3h9m0 0l-3-3m3 3l-3 3"/>
                </svg>
            </button>
        </form>
    </div>
</div>