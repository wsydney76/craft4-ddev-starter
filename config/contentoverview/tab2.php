<?php
/** @var wsydney76\contentoverview\services\ContentOverviewService $co */

use modules\main\models\ArticleSection;


return [
    $co->createColumn(6, [
        $co->createSection(ArticleSection::class)
            ->heading('Drafts')
            ->scope('drafts'),

        $co->createSection(ArticleSection::class)
            ->heading('My Provisional Drafts')
            ->scope('provisional')
            ->ownDraftsOnly(true),

        $co->createSection(ArticleSection::class)
            ->heading('Pending')
            ->status('pending'),

        $co->createSection(ArticleSection::class)
            ->heading('Disabled')
            ->status('disabled'),
    ]),

    $co->createColumn(6, [
        (new ArticleSection())
            ->heading('Latest Live Articles')
            ->layout('list')
            ->status('live')
            ->info('{postDate|date("short")}')
    ])

];
