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
    'heroTransform' => [
        'full' => ['width' => 2000, 'height' => 600],
        'xl' => ['width' => 1280, 'height' => 600]
    ],
    'heroSrcSet' => [
        'full' => [2000, 1580, 1024, 768, 640, 360],
        'xl' => [1280, 768, 640, 360]
    ],


    'cardTransform' => ['width' => 450, 'height' => 250],
    'cardletTransform' => ['width' => 220, 'height' => 120],

    'contentTransform' => [
        'default' => ['width' => 768],
        'wide' => ['width' => 1024],
    ],
    'contentSrcSet' => [
        'default' => [768, 400],
        'wide' => [1024, 768, 400],
    ],

    'featuredTransform' => ['width' => 1024, 'height' => 350],
    'featuredSrcSet' => [1024, 768, 400],

    'defaultTransform' => ['width' => 1024],
    'defaultSrcSet' => [1024, 640, 400],


    'lightBoxTransform' => ['height' => 800],

    'thumbTransform' => ['width' => 320, 'height' => 250],

    'mediaWidth' => 768,

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
