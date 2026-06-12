<div class="bg-white h-20 border-b flex justify-end items-center px-8 gap-6 shadow-md">
 
@if(Auth::user()->role == 'technieker')

<a
    href="{{ route('cart.index') }}"
    class="relative hover:scale-110 transition">

    <img
        src="{{ asset('images/cart.png') }}"
        alt="Winkelmandje"
        class="w-10 h-10">

    @if(session()->has('cart') && count(session('cart')) > 0)

        <span
            class="absolute -top-1 -right-1
                   bg-red-500 text-white
                   text-xs font-bold
                   rounded-full
                   w-5 h-5
                   flex items-center justify-center">

            {{ count(session('cart')) }}

        </span>

    @endif

</a>

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

        {{ substr(Auth::user()->name, 0, 1) }}

    </div>

    <span class="font-medium text-gray-700">

        {{ explode(' ', Auth::user()->name)[0] }}

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
        class="flex items-center justify-center
               w-10 h-10
               hover:scale-110 transition">

        <img
            src="{{ asset('images/logout.png') }}"
            alt="Logout"
            class="w-10 h-10">

    </button>

</form>

</div>