<?php

namespace App\View\Components;

use App\Models\Material;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Blade-component voor het tonen van een materiaalkaart.
 *
 * Deze component geeft een materiaal door aan de Blade-view
 * `components.material-card`. De kaart kan in een gewone of compacte
 * variant weergegeven worden.
 *
 * Wordt gebruikt in het materialenoverzicht en in de sectie met
 * aanbevolen materialen.
 */
class MaterialCard extends Component
{
    /**
     * Het materiaal dat in de kaart wordt weergegeven.
     *
     * @var Material
     */
    public Material $material;

    /**
     * Bepaalt of de compacte kaartweergave gebruikt wordt.
     *
     * @var bool
     */
    public bool $compact;

    /**
     * Maak een nieuwe MaterialCard-component aan.
     *
     * @param Material $material Het materiaal dat getoond wordt.
     * @param bool $compact Of de compacte weergave gebruikt moet worden.
     */
    public function __construct(Material $material, bool $compact = false)
    {
        $this->material = $material;
        $this->compact = $compact;
    }

    /**
     * Render de Blade-view van de materiaalkaart.
     *
     * @return View|Closure|string De view die de materiaalkaart weergeeft.
     */
    public function render(): View|Closure|string
    {
        return view('components.material-card');
    }
}
