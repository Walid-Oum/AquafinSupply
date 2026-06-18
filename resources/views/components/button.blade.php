{{--
    BUTTON COMPONENT
    Herbruikbare knop met Aquafin stijl (blauw).
    @author 
    @version 1.0
--}}

<button
    {{ $attributes->merge([
        'class' => 'bg-[#0F4C81] hover:bg-[#1E6BA8] text-white px-5 py-2 rounded-lg transition'
    ]) }}>

    {{ $slot }}

</button>