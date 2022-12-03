<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DatatableSource extends Component
{
    public $dom;
    public $compid;
    public $init;
    public $columns;
    public $height;
    public $compidmodal;
    public $select;
    public $number;
    public $widthNumber;
    public $className;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dom, $compid, $columns, $init = null, $height = null, $compidmodal = null, $select = null, $number = null, $widthNumber = null, $className = null)
    {
        if ($init === null) {
            $init = 'true';
        }

        if ($height === null) {
            $height = 0;
        }

        if ($compidmodal === null) {
            $compidmodal = '';
        }

        if ($select === null) {
            $select = [true, 'single'];
        }

        if ($widthNumber === null) {
            $widthNumber = '10%';
        }

        if ($number === null) {
            $number = true;
        }

        if ($className === null) {
            $className = [];
        }

        $this->dom = $dom;
        $this->compid = $compid;
        $this->init = $init;
        $this->columns = $columns;
        $this->height = $height;
        $this->compidmodal = $compidmodal;
        $this->select = $select;
        $this->number = $number;
        $this->className = $className;
    }

    public function getTagHeight($height)
    {
        return ($height > 0) ? '" style="height:' . $height . 'px"' : 'fit-content-tabs"';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.datatable-source');
    }
}
