<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormHorizontal extends Component
{
    public $error;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($error = true)
    {
        $this->error = $error;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.form-horizontal');
    }
}
