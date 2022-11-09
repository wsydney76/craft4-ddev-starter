<?php
/** @var wsydney76\contentoverview\services\ContentOverviewService $co */

return [
    $co->createColumn(7, [
        $co->createSection()
            ->section('news')
            ->heading('Latest News')
            ->info('{tagline}, {postDate|date("short")}')
            ->imageField('featuredImage')
            ->layout('cards')
            ->limit(6)
    ]),

    $co->createColumn(5, [
        $co->createSection()
            ->section('heroArea')
            ->limit(2)
            ->info('{heroTagline}')
            ->imageField('heroImage')
            ->layout('cardlets'),

        $co->createSection()
            ->section('page')
            ->heading('Page Structure')
            ->info('{isDraft ? "Draft"} {type.name}')
            ->icon('@appicons/globe.svg')
            ->scope('all')
    ]),


    $co->createColumn(4, [
        $co->createSection()
            ->section('legal')
            ->info('{type.name}')
    ])

];
