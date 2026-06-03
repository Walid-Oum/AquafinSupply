{{-- 
    Pagina: Detail bestelling

    User Stories:
    US17 - Inhoud bestelling bekijken
--}}
{{-- 
US17 - Inhoud bestelling bekijken
--}}

<x-app-layout>

    <x-page-header title="Bestelling Detail"/>

    <x-card>

        <div class="space-y-4">

            <div>
                <strong>Bestelling ID:</strong> #{{ $id }}
            </div>

            <div>
                <strong>Technieker:</strong> Samia
            </div>

            <div>
                <strong>Leverdatum:</strong> 15/06/2026
            </div>

            <div>
                <strong>Status:</strong>

                <x-status-badge status="Nieuw"/>
            </div>

            <div>
                <strong>Opmerking:</strong>

                Materiaal nodig voor onderhoudswerken.
            </div>

        </div>

    </x-card>

    <div class="mt-6">

        <x-card>

            <h2 class="text-xl font-semibold mb-4">

                Bestelde materialen

            </h2>

            <table class="w-full">

                <thead>

                    <tr class="border-b">

                        <th class="text-left p-3">
                            Materiaal
                        </th>

                        <th class="text-left p-3">
                            Hoeveelheid
                        </th>

                    </tr>

                </thead>

                <tbody>

                    {{-- Tijdelijke voorbeelddata --}}

                    <tr class="border-b">

                        <td class="p-3">
                            Materiaal 1
                        </td>

                        <td class="p-3">
                            2
                        </td>

                    </tr>

                    <tr>

                        <td class="p-3">
                            Materiaal 2
                        </td>

                        <td class="p-3">
                            1
                        </td>

                    </tr>

                </tbody>

            </table>

        </x-card>

    </div>

</x-app-layout>