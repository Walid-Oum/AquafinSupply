<x-app-layout>

    <x-page-header title="Mijn profiel" />

    <div class="max-w-5xl mx-auto space-y-6">

        <x-card>
            @include('userzone.profile.partials.update-profile-information-form')
        </x-card>

        <x-card>
            @include('userzone.profile.partials.show-location')
        </x-card>

        <x-card>
            @include('userzone.profile.partials.update-password-form')
        </x-card>


    </div>

</x-app-layout>
