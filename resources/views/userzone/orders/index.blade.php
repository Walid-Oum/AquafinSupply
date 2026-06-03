{{-- 
    Pagina: Overzicht bestellingen

    User Stories:
    US12 - Eigen bestellingen bekijken
    US15 - Alle bestellingen bekijken
--}}
<x-app-layout>

    <x-page-header title="Bestellingen"/>

    <x-card>

        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="p-3 text-left">
                        ID
                    </th>

                    <th class="p-3 text-left">
                        Technieker
                    </th>

                    <th class="p-3 text-left">
                        Leverdatum
                    </th>

                    <th class="p-3 text-left">
                        Status
                    </th>

                    <th class="p-3 text-left">
                        Actie
                    </th>

                </tr>

            </thead>

            <tbody>

                {{-- Tijdelijke voorbeelddata --}}

                <tr class="border-b">

                    <td class="p-3">
                        #001
                    </td>

                    <td class="p-3">
                        Samia
                    </td>

                    <td class="p-3">
                        15/06/2026
                    </td>

                    <td class="p-3">

                        <x-status-badge status="Nieuw"/>

                    </td>

                    <td class="p-3">

                        <a href="/bestellingen/1">

                            <x-button>
                                Bekijken
                            </x-button>

                        </a>

                    </td>

                </tr>

                <tr>

                    <td class="p-3">
                        #002
                    </td>

                    <td class="p-3">
                        Samia
                    </td>

                    <td class="p-3">
                        18/06/2026
                    </td>

                    <td class="p-3">

                        <x-status-badge status="In voorbereiding"/>

                    </td>

                    <td class="p-3">

                        <a href="/bestellingen/2">

                            <x-button>
                                Bekijken
                            </x-button>

                        </a>

                    </td>

                </tr>

            </tbody>

        </table>

    </x-card>

</x-app-layout>