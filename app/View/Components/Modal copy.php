<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{

    public $dom;
    public $compid;
    public $size;
    public $title;
    public $close;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dom, $compid, $size = '', $title = '', $close = 'true')
    {
        $this->dom = $dom;
        $this->compid = $compid;
        $this->size = $size;
        $this->title = $title;
        $this->close = $close;
    }

    public function getSize($size){
        return ($size != '') ? 'modal-'.$size : '' ;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
