<?php
/** @var wsydney76\contentoverview\services\ContentOverviewService $co */

return [
    $co->createColumn(7, [
        $co->createSection()
            ->section('news')
            ->entryType('default')
            ->heading('Latest News')
            ->info('{tagline}, {postDate|date("short")}')
            ->imageField('featuredImage')
            ->layout('cards')
            ->size('small')
            ->limit(3),
        $co->createSection()
            ->section('news')
            ->entryType('story')
            ->heading('Latest Stories')
            ->info('{tagline}, {postDate|date("short")}')
            ->imageField('featuredImage')
            ->layout('cards')
            ->size('small')
            ->limit(3),
        $co->createSection()
            ->section('legal')
            ->info('{type.name}')
    ]),



    $co->createColumn(5, [
        $co->createSection()
            ->section('heroArea')
            ->limit(2)
            ->info('{body}')
            ->imageField('image')
            ->layout('cardlets'),

        $co->createSection()
            ->section('page')
            ->heading('Page Structure')
            ->info('{isDraft ? "Draft"} {type.name}')
            ->icon('@appicons/globe.svg')
            ->scope('all')
    ]),


];
