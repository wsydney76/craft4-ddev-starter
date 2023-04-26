<?php

// https://github.com/vaersaagod/seomate/blob/master/README.md

use craft\elements\Entry;

return [
    '*' => [
        'cacheEnabled' => false,
        'defaultProfile' => 'standard',

        'outputAlternate' => true,

        'altTextFieldHandle' => 'altText',

        'defaultMeta' => [
            'description' => ['siteInfo.seoDescription'],
            'image' => ['siteInfo.seoImage'],
        ],

        'fieldProfiles' => [
            'standard' => [
                'title' => ['seoFields.settings:alternativeTitle', 'title'],
                'description' => ['seoFields.settings:description', 'tagline'],
                'image' => ['seoFields.settings:image', 'featuredImage'],
                'robots' => ['seoFields.settings:robots'],
            ],
        ],

        'sitemapEnabled' => true,
        'sitemapLimit' => 100,
        'sitemapConfig' => [
            'elements' => [
                'frontpages' => [
                    'elementType' => Entry::class,
                    'criteria' => ['section' => 'page','type' => ['home', 'articleIndex']],
                    'params' => ['changefreq' => 'daily', 'priority' => 1],
                ],
                'page' => ['changefreq' => 'daily', 'priority' => 0.5],
                'article' => ['changefreq' => 'weekly', 'priority' => 0.5],
                'legal' => ['changefreq' => 'yearly', 'priority' => 0.2],
            ],
        ],

        'siteName' => Craft::$app->getSystemName(),
        'sitenameSeparator' => ' - '
    ],

    'production' => [
        'cacheEnabled' => true,
        'siteName' => Craft::$app->getSystemName(),
    ]

];
