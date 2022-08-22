<?php

// https://github.com/vaersaagod/seomate/blob/master/README.md

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
                'page' => ['changefreq' => 'daily', 'priority' => 1],
                'news' => ['changefreq' => 'daily', 'priority' => 1],
                'legal' => ['changefreq' => 'daily', 'priority' => 1],
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
