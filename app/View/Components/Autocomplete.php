<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Autocomplete extends Component
{
    public $dom;
    public $compid;
    public $url;
    public $default;
    public $clear;
    public $size;
    public $dropdowncompid;
    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dom, $compid, $url, $type = 'serverside', $default = '', $clear = 'false', $size = '', $dropdowncompid = '')
    {
        $this->dom = $dom;
        $this->compid = $compid;
        $this->url = $url;
        $this->type = $type;
        $this->default = $default;
        $this->clear = $clear;
        $this->size = $size;
        $this->dropdowncompid = $dropdowncompid;
    }

    public function getSizeContainer($size)
    {
        return ($size != '') ? 'containerCssClass:"select-' . $size . '",' : '';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.autocomplete');
    }
}
