<?php
/** @var wsydney76\contentoverview\services\ContentOverviewService $co */

use modules\main\models\ArticleSection;


return [
    $co->createColumn(6, [
        $co->createSection(ArticleSection::class)
            ->heading('Drafts')
            ->scope('drafts')
            ->actions(['relationships','view','compare']),

        $co->createSection(ArticleSection::class)
            ->heading('My Provisional Drafts')
            ->scope('provisional')
            ->ownDraftsOnly(true)
            ->actions(['relationships','view','compare']),

        $co->createSection(ArticleSection::class)
            ->heading('Pending')
            ->status('pending')
            ->actions(['relationships','view']),

        $co->createSection(ArticleSection::class)
            ->heading('Disabled')
            ->status('disabled')
            ->actions(['relationships','view']),
    ]),

    $co->createColumn(6, [
        (new ArticleSection())
            ->heading('Latest Live Articles')
            ->layout('list')
            ->status('live')
            ->info('{postDate|date("short")}')
            ->actions(['relationships','view','compare'])
    ])

];
