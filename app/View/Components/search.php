<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class search extends Component
{
    public $perPage;
    public $action;
    public $value;
    public $placeholder;

    public function __construct($action = '#', $value = '', $placeholder = 'Type to search...', $perPage=5)
    {
         $this->perPage = $perPage;
        $this->action = $action;
        $this->value = $value;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.search');
    }
}
