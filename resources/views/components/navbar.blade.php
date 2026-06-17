@php
    $user = Auth::user();

    $cartCount = collect(session('cart', []))->sum('quantity');

    $canSeeNotifications = in_array($user->role, ['technieker', 'magazijn']);

    $unreadNotificationCount = $canSeeNotifications
        ? $user->userNotifications()->whereNull('read_at')->count()
        : 0;

    $latestNotifications = $canSeeNotifications
        ? $user->userNotifications()->latest()->take(5)->get()
        : collect();
@endphp

<div class="bg-white h-20 border-b flex justify-end items-center px-8 gap-6 shadow-md">

    @if($user->role === 'technieker')
        <a
            href="{{ route('cart.index') }}"
            class="relative hover:scale-110 transition"
            title="Winkelmandje"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="1.8"
                 stroke="#0F4C81"
                 class="w-10 h-10">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M2.25 3h1.386a1.5 1.5 0 011.464 1.175L5.383 6.75m0 0h13.867l-1.2 6H6.383m-1-6L6.75 15m0 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm10.5 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"/>
            </svg>

            <span
                id="cart-count"
                class="absolute -top-1 -right-1
                       bg-red-500 text-white
                       text-xs font-bold
                       rounded-full
                       w-5 h-5
                       flex items-center justify-center
                       {{ $cartCount > 0 ? '' : 'hidden' }}"
            >
                {{ $cartCount }}
            </span>
        </a>
    @endif

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
                class="relative hover:scale-110 transition"
                title="Notificaties"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.8"
                     stroke="#0F4C81"
                     class="w-10 h-10">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M14.857 17.082a2.25 2.25 0 01-4.714 0m8.607-2.332A3.375 3.375 0 0117.25 12V9.75a5.25 5.25 0 00-10.5 0V12a3.375 3.375 0 01-1.5 2.75l-.33.22a.75.75 0 00.416 1.38h13.328a.75.75 0 00.416-1.38l-.33-.22z"/>
                </svg>

                <span
                    id="notification-count"
                    class="absolute -top-1 -right-1
                           bg-red-500 text-white
                           text-xs font-bold
                           rounded-full
                           min-w-5 h-5
                           px-1
                           flex items-center justify-center
                           {{ $unreadNotificationCount > 0 ? '' : 'hidden' }}"
                >
                    {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                </span>
            </button>

            <div
                x-show="open"
                @click.outside="open = false"
                x-cloak
                class="absolute right-0 mt-3 w-96 rounded-xl bg-white shadow-xl border border-gray-200 z-50"
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

    <a href="{{ route('profile.edit') }}"
       class="flex items-center gap-3 hover:opacity-80 transition">

        <div
            class="w-10 h-10 rounded-full
                   bg-[#0F4C81]
                   text-white
                   flex items-center
                   justify-center
                   font-bold
                   uppercase">

            {{ substr($user->name, 0, 1) }}
        </div>

        <span class="font-medium text-gray-700">
            {{ explode(' ', $user->name)[0] }}
        </span>
    </a>

    <div class="h-10 w-px bg-gray-300"></div>

    <form
        method="POST"
        action="{{ route('logout') }}"
        class="flex items-center">

        @csrf

        <button
            type="submit"
            class="hover:scale-110 transition"
            title="Uitloggen">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.8"
                stroke="#0F4C81"
                class="w-10 h-10">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m-3-3h9m0 0l-3-3m3 3l-3 3"/>
            </svg>
        </button>
    </form>
</div>
