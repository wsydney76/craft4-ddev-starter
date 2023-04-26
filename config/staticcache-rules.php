<?php

// Config file for wsydn/staticcache plugin

// bash setup/plugin-ddev
// ddev composer require wsydn/craft-staticcache
// ddev craft plugin/install _staticcache

return [
    'exclude' => [
        'en' => [
            '*contact*'
        ],
        'de' => [
            '*kontakt*'
        ],
    ],

    'paginateTasks' => [
        [
            'criteria' => [
                'section' => 'article',
            ],
            'uri' => [
                'en' => 'articles',
                'de' => 'artikel',
            ]
        ],
    ]
];