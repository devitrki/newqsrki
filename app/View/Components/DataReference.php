<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DataReference extends Component
{
    public $dom;
    public $compid;
    public $title;
    public $size;
    public $url;
    public $columns;
    public $height;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dom, $compid, $title, $size, $url, $columns, $height)
    {
        $this->dom = $dom;
        $this->compid = $compid;
        $this->title = $title;
        $this->size = $size;
        $this->url = $url;
        $this->columns = $columns;
        $this->height = $height;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.data-reference');
    }
}
