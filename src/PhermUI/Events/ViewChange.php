<?php

namespace MilesChou\PhermUI\Events;

use MilesChou\PhermUI\View\View;

class ViewChange
{
    /** @var View */
    public $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }
}
