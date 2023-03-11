<?php

use modules\main\helpers\HtmlDumper;

return [
    'components' => [
        'dumper' => function() {
            $dumper = new HtmlDumper();
            $dumper->setTheme('light');
            return $dumper;
        },
    ],
];