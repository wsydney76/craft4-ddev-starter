<?php

return [
    'stickyMenu' => false,

    'entriesPerPage' => 12,
    'latestNewsLimit' => 6,

    // The width of the primary navigation, defined in nav.twig
    // container|2xl|xl|lg
    'navWidth' => 'container',

    // Breakpoint when to show the mobile nav (hamburger), defined in nav.twig
    // md|lg|xl|2xl|never|always
    'mobileNavBreakpoint' => 'md',

    'defaultImageFormat' => 'webp',

    // full/xl, as defined in hero-area-display.twig
    'heroWidth' => 'full',

    // template in _layouts/partials/heroarea
    'heroTemplate' => 'default',
    'heroFallbackTemplate' => 'textonly',


    // We define image transforms/srcsets here if they are reused among different components

    // Reused among different hero templates
    'heroTransform' => [
        'full' => ['width' => 2000, 'height' => 600],
        'xl' => ['width' => 1280, 'height' => 600]
    ],
    'heroSrcSet' => [
        'full' => [2000, 1580, 1024, 768, 640, 360],
        'xl' => [1280, 768, 640, 360]
    ],

    // Reused for eager loading transform records
    'cardTransform' => ['width' => 450, 'height' => 250],
    'cardletTransform' => ['width' => 220, 'height' => 120],

    // Fallback, used for seo/json-ld
    'defaultTransform' => ['width' => 1024],
    'defaultSrcSet' => [1024, 640, 400],

    'searchSections' => ['news', 'page', 'legal'],
    'searchMaxResults' => 24,

    'sitemapSections' => [
        ['handle' => 'page'],
        ['handle' => 'legal'],
        ['handle' => 'news', 'orderBy' => 'postDate desc', 'limit' => 10, 'moreType' => 'newsIndex', 'moreText' => Craft::t('site', 'All News')],
    ],

    // This setting allows plugins to inject section specific content via their own templates.
    'sectionRoots' => [
        '_sections',
    ],

    // this is the file as in sprig.script(), just hosting it locally. Check version after updating sprig.
    // Gets versioned by {% js craft.app.config.custom.htmxScriptUrl %} so it is safe to always use the same path
    // Copy from e.g. https://unpkg.com/htmx.org@1.8.0/dist/htmx.min.js
    'htmxScriptUrl' => '/assets/vendor/htmx/htmx.min.js', // 1.8.0

    // Preload fonts used above the fold
    'preloadFonts' => [
        '/assets/fonts/raleway-v22-latin-700.woff2',
        '/assets/fonts/open-sans-v27-latin-regular.woff2',
        '/assets/fonts/open-sans-v27-latin-600.woff2'
    ]

];
