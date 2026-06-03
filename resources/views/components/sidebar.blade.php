@php

// TEMPORAIRE POUR LES TESTS

$role = 'technieker';

// $role = 'magazijn';

// $role = 'admin';

@endphp

<div class="w-72 bg-gradient-to-b from-[#0F4C81] via-[#1E6BA8] to-[#2D7FC1] text-white flex flex-col shadow-xl">

    <div class="p-8 border-b border-blue-400">

        <div class="flex items-center gap-3">

            <div
                class="bg-white text-[#0F4C81]
                       w-12 h-12 rounded-xl
                       flex items-center justify-center
                       font-bold text-xl">

                A

            </div>

            <div>

                <h1 class="text-3xl font-bold">

                    Aquafin

                </h1>

                <p class="text-blue-100 text-sm">

                    Supply App

                </p>

            </div>

        </div>

    </div>

    <nav class="flex-1 p-5">

        <ul class="space-y-2">

            {{-- TECHNIEKER --}}

            @if($role == 'technieker')

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Materialen
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Winkelmandje
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Bestellingen
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Tickets
                    </a>
                </li>

            @endif

            {{-- MAGAZIJN --}}

            @if($role == 'magazijn')

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Bestellingen
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Voorraad
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Tickets
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Overstromingsrisico
                    </a>
                </li>

            @endif

            {{-- ADMIN --}}

            @if($role == 'admin')

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Gebruikers
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                        Materialen
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="block px-4 py-3 rounded-lg hover:bg-blue-500 transition">
                         Bestellingen
                    </a>
                </li>

            @endif

        </ul>

    </nav>

    <div class="p-5 border-t border-blue-400">

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="w-full bg-red-500 hover:bg-red-600 transition py-3 rounded-lg font-medium">

                Uitloggen

            </button>

        </form>

    </div>

</div>