<?php

namespace App\Abstractions\AbstractClasses;

use Illuminate\Support\Facades\View;

abstract class EmailClass
{
    /**
     * @param string $bladeTemplate
     * @param array $bladeData
     * @return string
     */
    public function loadTemplateView(string $bladeTemplate, array $bladeData = []): string
    {
        $view = View::make($bladeTemplate, $bladeData);

        return $view->render();//fetch the content of the blade template
    }
}
