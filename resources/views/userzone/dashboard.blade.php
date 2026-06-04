<x-app-layout>

    <div class="bg-white rounded-xl shadow p-8">

        <h1 class="text-3xl font-bold text-[#0F4C81]">

            Welkom {{ Auth::user()->name }}

        </h1>

        <p class="mt-3 text-gray-600">

            Je bent ingelogd als
            <span class="font-semibold">
                {{ Auth::user()->role }}
            </span>

        </p>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

        @if(Auth::user()->role == 'technieker')

            <x-card>

                <h2 class="text-xl font-semibold mb-2">
                    Technieker
                </h2>

                <p class="text-gray-600">
                    Bekijk materialen, beheer je winkelmandje en volg je bestellingen op.
                </p>

            </x-card>

        @endif

        @if(Auth::user()->role == 'magazijn')

            <x-card>

                <h2 class="text-xl font-semibold mb-2">
                    Magazijnmedewerker
                </h2>

                <p class="text-gray-600">
                    Verwerk bestellingen, beheer voorraad en volg tickets op.
                </p>

            </x-card>

        @endif

        @if(Auth::user()->role == 'admin')

            <x-card>

                <h2 class="text-xl font-semibold mb-2">
                    Administrator
                </h2>

                <p class="text-gray-600">
                    Beheer gebruikers, rollen en materialen.
                </p>

            </x-card>

        @endif

    </div>

</x-app-layout>
