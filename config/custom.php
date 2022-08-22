<?php

return [
    'entriesPerPage' => 12,
    'latestNewsLimit' => 8,

    'defaultTransform' => ['width' => 1024],
    'defaultSrcSet' => [1024, 640, 400],

    'heroTransform' => ['width' => 2000, 'height' => 600],
    'heroSrcSet' => [2000, 1580, 1024, 768, 640, 360],


    'cardTransform' => ['width' => 450, 'height' => 300],
    'defaultImageFormat' => 'webp',

    'contentTransform' => ['width' => 768, 'height' => 432],
    'contentSrcSet' => [768, 400],

    'featuredTransform' => ['width' => 1024, 'height' => 350],
    'featuredSrcSet' => [1024, 768, 400],

    'lightBoxTransform' => ['height' => 800],

    'thumbTransform' => ['width' => 320, 'height' => 250],

    'mediaWidth' => 768,

    'searchSections' => ['news', 'page', 'legal'],
    'searchMaxResults' => 24,

    // this is the file as in sprig.script(), just hosting it locally. Check version after updating sprig.
    // Gets versioned by {% js craft.app.config.custom.htmxScriptUrl %} so it is safe to always use the same path
    'htmxScriptUrl' => '/assets/vendor/htmx/htmx.min.js' // 1.7.0
];
