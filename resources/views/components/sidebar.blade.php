@php
    $role = Auth::user()->role;
@endphp

<div class="w-72 h-screen sticky top-0 text-white flex flex-col shadow-2xl relative overflow-hidden">

    {{-- Background --}}
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/sidebar-bg.jpg') }}"
            alt="Aquafin"
            class="w-full h-full object-cover">
    </div>

    {{-- Overlay léger --}}
    <div
        class="absolute inset-0
               bg-gradient-to-b
               from-[#0F4C81]/70
               via-[#1E6BA8]/50
               to-[#0F4C81]/80">
    </div>

    <div class="relative z-10 flex flex-col h-full">

        {{-- Logo --}}
        <div class="p-6 border-b border-white/20 backdrop-blur-sm">

            <div class="flex justify-center">

                <a
                    href="
                @if($role == 'admin')
                    {{ route('admin.users.index') }}
                @elseif($role == 'magazijn')
                    {{ route('magazijn.materials.index') }}
                @else
                    {{ route('technician.materials.index') }}
                @endif
            "
                >
                    <img
                        src="{{ asset('images/aquafin-logo.png') }}"
                        alt="Aquafin"
                        class="w-44 object-contain hover:scale-105 transition-transform duration-200 cursor-pointer">
                </a>

            </div>

        </div>

        {{-- Menu --}}
        <nav class="flex-1 px-4 py-6">

            <ul class="space-y-3">

                @if($role == 'technieker')

                    <li>
                        <a href="{{ route('technician.materials.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">

                            <span>Materialen</span>
                        </a>
                    </li>



                    <li>
                        <a href="{{ route('orders.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">

                            <span>Bestellingen</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('tickets.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">

                            <span>Support</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('flood-risk.index')}}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                            Overstromingsrisico
                        </a>
                    </li>

                @endif

                @if($role == 'magazijn')

                    <li>
                        <a href="{{ route('magazijn.orders.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                         Bestellingen
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('magazijn.materials.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                             Voorraad
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('tickets.warehouse.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                             Support aanvragen
                        </a>
                    </li>

                    <li>
                        <a href="{{route('flood-risk.index')}}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                            Overstromingsrisico
                        </a>
                    </li>

                @endif

                @if($role == 'admin')

                    <li>
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                            Gebruikers
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('materials.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                            Materialen
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.orders.index') }}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                            Bestellingen
                        </a>
                    </li>
                     <li>
                        <a href="{{route('admin.flood-risk.index')}}"
                           class="flex items-center font-bold gap-3 px-4 py-3 rounded-xl hover:bg-white/15 transition-all">
                            Overstromingsrisico
                        </a>
                    </li>

                @endif

            </ul>

        </nav>

    </div>

</div>
