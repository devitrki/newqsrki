<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DatatableServerside extends Component
{
    public $dom;
    public $compid;
    public $init;
    public $url;
    public $columns;
    public $number;
    public $widthNumber;
    public $order;
    public $footer;
    public $height;
    public $lengthMenu;
    public $rowReorder;
    public $colReorder;
    public $scroller;
    public $fixedColumns;
    public $select;
    public $tabmenu;
    public $compidmodal;
    public $dblclick;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dom, $compid, $url, $columns, $init = null, $number = null, $widthNumber = null, $order = null, $footer = null, $height = null, $lengthMenu = null, $rowReorder = null, $colReorder = null, $scroller = null, $fixedColumns = null, $select = null, $tabmenu = null, $compidmodal = null, $dblclick = null)
    {
        if ($init === null) {
            $init = 'true';
        }

        if ($number === null) {
            $number = 'true';
        }

        if ($widthNumber === null) {
            $widthNumber = '10';
        }

        if ($order === null) {
            $order = [];
        }

        if ($footer === null) {
            $footer = 'true';
        }

        if ($height === null) {
            $height = 0;
        }

        if ($lengthMenu === null) {
            $lengthMenu = [[50, 100, 150, 200, 250], [50, 100, 150, 200, 250]];
        }

        if ($rowReorder === null) {
            $rowReorder = 'false';
        }

        if ($colReorder === null) {
            $colReorder = 'false';
        }

        if ($scroller === null) {
            $scroller = 'false';
        }

        if ($fixedColumns === null) {
            $fixedColumns = [false, 0];
        }

        if ($select === null) {
            $select = [false, 'single'];
        }

        if ($tabmenu === null) {
            $tabmenu = '';
        }

        if ($compidmodal === null) {
            $compidmodal = '';
        }

        if ($dblclick === null) {
            $dblclick = false;
        }

        $this->dom = $dom;
        $this->compid = $compid;
        $this->init = $init;
        $this->url = $url;
        $this->columns = $columns;
        $this->number = $number;
        $this->widthNumber = $widthNumber;
        $this->order = $order;
        $this->footer = $footer;
        $this->height = $height;
        $this->lengthMenu = $lengthMenu;
        $this->rowReorder = $rowReorder;
        $this->colReorder = $colReorder;
        $this->scroller = $scroller;
        $this->fixedColumns = $fixedColumns;
        $this->select = $select;
        $this->tabmenu = $tabmenu;
        $this->compidmodal = $compidmodal;
        $this->dblclick = $dblclick;

        if($number == 'true'){
            array_unshift($this->columns,[
                'label' => 'No',
                'data' => 'DT_RowIndex',
                'searchable' => 'false',
                'orderable' => 'false',
                'width' => $widthNumber,
            ]);
        }
    }

    public function getTagHeight($height){
        return ($height > 0) ? '" style="height:'.$height.'px"' : 'fit-content-tabs"' ;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.datatable-serverside');
    }
}
