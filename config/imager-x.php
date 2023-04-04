<?php
return [

    'imagerSystemPath' => '@webroot/assets/site/',
    'imagerUrl' => '@web/assets/site/',

    'fallbackImage' => '/assets/images/fallback.png',

    'fillTransforms ' => false,
    'fillInterval' => 256,

    'resizeFilter ' => 'lanczos',
    'webpQuality ' => 82,
    'jpegQuality  ' => 82,
    'useForNativeTransforms ' => false,

    'cacheDuration' => 10 * 365 * 24 * 60 * 60
];
