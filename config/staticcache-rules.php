<?php

// Config file for wsydn/staticcache plugin

// bash setup/plugin-ddev
// ddev composer require wsydn/craft-staticcache
// ddev craft plugin/install _staticcache

return [
    'paginate' => [
        [
            'criteria' => [
                'section' => 'news',
            ],
            'uri' => [
                'en' => 'news',
                'de' => 'news',
            ]
        ],
    ]
];