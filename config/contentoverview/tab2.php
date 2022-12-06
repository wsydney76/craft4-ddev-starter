<?php
/** @var wsydney76\contentoverview\services\ContentOverviewService $co */

use modules\main\models\NewsSection;


return [
    $co->createColumn(6, [
        $co->createSection(NewsSection::class)
            ->heading('Drafts')
            ->scope('drafts'),

        $co->createSection(NewsSection::class)
            ->heading('My Provisional Drafts')
            ->scope('provisional')
            ->ownDraftsOnly(true),

        $co->createSection(NewsSection::class)
            ->heading('Pending')
            ->status('pending'),

        $co->createSection(NewsSection::class)
            ->heading('Disabled')
            ->status('disabled'),
    ]),

    $co->createColumn(6, [
        (new NewsSection())
            ->heading('Latest Live News')
            ->layout('list')
            ->status('live')
            ->info('{postDate|date("short")}')
    ])

];
