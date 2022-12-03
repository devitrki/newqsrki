<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RowHorizontal extends Component
{

    public $label;
    public $desc;
    public $id;
    public $descId;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $desc = '', $id = '', $descId = '')
    {
        $this->label = $label;
        $this->desc = $desc;
        $this->id = $id;
        $this->descId = $descId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.row-horizontal');
    }
}
