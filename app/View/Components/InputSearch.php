<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputSearch extends Component
{
    public $dom;
    public $dtblecompid;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dom, $dtblecompid)
    {
        $this->dom = $dom;
        $this->dtblecompid = $dtblecompid;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.input-search');
    }
}
