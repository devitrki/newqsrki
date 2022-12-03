<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RowVertical extends Component
{
    public $label;
    public $desc;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $desc = '')
    {
        $this->label = $label;
        $this->desc = $desc;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.row-vertical');
    }
}
