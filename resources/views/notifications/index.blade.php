<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <x-page-header title="Notificaties" />

            <p class="text-gray-600">
                Bekijk hier updates over je bestellingen en supportaanvragen.
            </p>
        </div>

        @forelse($notifications as $notification)
            <a
                href="{{ $notification->link ?? '#' }}"
                class="mb-4 block rounded-lg bg-white p-4 shadow hover:bg-gray-50"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-gray-900">
                            {{ $notification->title }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $notification->message }}
                        </p>

                        <p class="mt-2 text-xs text-gray-400">
                            {{ $notification->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    @if(! $notification->is_read)
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                            Nieuw
                        </span>
                    @endif
                </div>
            </a>
        @empty
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-gray-600">
                    Je hebt nog geen notificaties.
                </p>
            </div>
        @endforelse
    </div>
</x-app-layout>
