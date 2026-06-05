<div class="bg-white h-20 border-b flex justify-end items-center px-8">

    <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 p-2 rounded-xl hover:bg-gray-50 transition-colors duration-150 cursor-pointer group">

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

            <p class="font-semibold text-gray-700 leading-tight">
                {{ Auth::user()->name }}
            </p>

            <p class="text-xs text-gray-400 group-hover:text-[#1E6BA8] transition-colors capitalize">
                {{ Auth::user()->role }} • <span class="underline">Wijzigen</span>
            </p>

        </div>

    </a>

</div>
