<?php

use modules\main\helpers\CustomHtmlDumper;

return [
    'components' => [
        'dumper' => function() {
            $dumper = new CustomHtmlDumper();
            $dumper->setTheme('light');
            return $dumper;
        },
    ],
];