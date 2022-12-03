<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Pickerdate extends Component
{

    public $dom;
    public $compid;
    public $clear;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dom, $compid, $clear = 'true')
    {
        $this->dom = $dom;
        $this->compid = $compid;
        $this->clear = $clear;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.pickerdate');
    }
}
