<?php

namespace App\View\Components;

use App\Models\Material;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MaterialCard extends Component
{
    public Material $material;
    public bool $compact;

    public function __construct(Material $material, bool $compact = false)
    {
        $this->material = $material;
        $this->compact = $compact;
    }

    public function render(): View|Closure|string
    {
        return view('components.material-card');
    }
}
