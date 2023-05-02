<?php

return [
    'stickyMenu' => false,

    'entriesPerPage' => 12,
    'latestArticlesLimit' => 6,

    // The width of the primary navigation, defined in nav.twig
    // container|2xl|xl|lg
    'navWidth' => 'container',

    // Whether to show level 3 pages in a dropdown in the main navigation
    'showChildrenInMainNav' => false,

    // Breakpoint when to show the mobile nav (hamburger), defined in nav.twig
    // md|lg|xl|2xl|never|always
    'mobileNavBreakpoint' => 'md',

    'defaultImageFormat' => 'webp',

    // full/xl, as defined in hero-area-display.twig
    'heroWidth' => 'full',

    // template in _layouts/partials/heroarea
    'heroTemplate' => 'default',
    'heroFallbackTemplate' => 'textonly',

    // Body content block types that control their own width
    'fullWidthBlocks' => ['contentComponents', 'quote'],

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
    'cardletTransform' => ['width' => 220, 'height' => 160],

    // Fallback, used for seo/json-ld
    'defaultTransform' => ['width' => 1024],
    'defaultSrcSet' => [1024, 640, 400],

    'searchSections' => ['article', 'page', 'legal', 'contentSection', 'heroArea', 'person'],
    'searchMaxResults' => 24,

    'sitemapSections' => [
        ['handle' => 'page'],
        ['handle' => 'legal'],
        ['handle' => 'article', 'orderBy' => 'postDate desc', 'limit' => 10, 'moreType' => 'articleIndex', 'moreText' => Craft::t('site', 'All Articles')],
    ],

    // This setting allows plugins to inject section specific content via their own templates.
    'sectionRoots' => [
        '_sections',
    ],

    // This setting allows plugins to overwrite section specific templates
    'sectionTemplates' => [

    ],

    // Settings for prepareText twig filter, used in text matrix blocks
    'purifierConfig' => 'Custom', // File in config/htmlpurifier
    'markdownFlavor' => 'extra', // extra|gfm|gfm-comments|original
    'accents' => 'italic', // strong|italic|default

    // Guides
    'showGuides' => [
        'sections' => ['article', 'page', 'legal', 'siteInfo']
    ],

    'guides' => [
        ['label' => 'Intro', 'template' => 'intro'],
        ['heading' => 'Sections'],
        ['label' => 'Article', 'template' => 'content/article'],
        ['label' => 'Page', 'template' => 'content/page'],
        ['label' => 'Legal', 'template' => 'content/legal'],
        ['label' => 'Site Info', 'template' => 'content/siteInfo'],
        ['heading' => 'Editing'],
        ['label' => 'Blocks', 'template' => 'content/blocks'],
        ['label' => 'Image Copyright', 'template' => 'content/image-copyright'],
    ],

    // Preload fonts used above the fold
    'preloadFonts' => [
        '/assets/fonts/raleway-v22-latin-700.woff2',
        '/assets/fonts/open-sans-v27-latin-regular.woff2',
        '/assets/fonts/open-sans-v27-latin-600.woff2'
    ],


    // Whether copyright notices should be by default shown in the footer or on the image
    // TODO: Implementation for 'show' is incomplete
    // register/show/none
    'handleCopyright' => 'show',

    // Whether to skip srcset generation for images
    // This is useful to speed up things in development when not using the imager-x plugins
    'skipSrcset' => true,

    // Whether to enable hot reloading in live preview.
    // Requires Craft PRO, and disabled auto refresh on all preview targets
    'enableHotReload' => false,

];
