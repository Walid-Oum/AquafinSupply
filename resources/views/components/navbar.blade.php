<div class="bg-white h-20 border-b flex justify-end items-center px-8">

    <div class="flex items-center gap-4">

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

            <p class="text-sm text-gray-500 capitalize">
                {{ Auth::user()->role }}
            </p>

        </div>

    </div>

</div>