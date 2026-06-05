<div class="bg-white h-20 border-b flex justify-end items-center px-8 gap-6 shadow-md">

    <a href="{{ route('profile.edit') }}"
       class="flex items-center gap-4 p-2 rounded-xl hover:bg-gray-50 transition">

        <div
            class="w-12 h-12 rounded-full
                   bg-gradient-to-r
                   from-[#0F4C81]
                   to-[#2D7FC1]
                   text-white
                   flex items-center
                   justify-center
                   font-bold
                   uppercase">

            {{ substr(Auth::user()->name, 0, 1) }}

        </div>

        <div>

            <p class="font-semibold text-gray-700">
                {{ Auth::user()->name }}
            </p>

            <p class="text-xs text-gray-400 capitalize">
                {{ Auth::user()->role }}
            </p>

        </div>

    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button
            type="submit"
            class="bg-[#0F4C81]
                   hover:bg-[#1E6BA8]
                   text-white
                   font-medium
                   px-5 py-2
                   rounded-xl
                   shadow-md
                   transition">

            Uitloggen

        </button>

    </form>

</div>