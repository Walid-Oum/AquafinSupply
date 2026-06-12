<div class="bg-white h-20 border-b flex justify-end items-center px-8 gap-6 shadow-md">
 
@if(Auth::user()->role == 'technieker')

<a
    href="{{ route('cart.index') }}"
    class="relative hover:scale-110 transition">

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
        class="hover:scale-110 transition">

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
                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m-3-3h9m0 0l-3-3m3 3l-3 3" />

        </svg>

    </button>

</form>
</div>