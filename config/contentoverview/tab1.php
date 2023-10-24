<?php
/** @var wsydney76\contentoverview\services\ContentOverviewService $co */

return [
    $co->createColumn(7, [
        $co->createSection()
            ->section('article')
            ->entryType('default')
            ->heading('Latest Articles')
            ->info('{tagline}, {postDate|date("short")}')
            ->imageField('featuredImage')
            ->layout('cards')
            ->size('small')
            ->actions(['relationships','view'])
            ->limit(3),
        $co->createSection()
            ->section('article')
            ->entryType('story')
            ->heading('Latest Stories')
            ->info('{tagline}, {postDate|date("short")}')
            ->imageField('featuredImage')
            ->layout('cards')
            ->size('small')
            ->actions(['relationships','view'])
            ->limit(3),
        $co->createSection()
            ->section('legal')
            ->actions(['relationships','view'])
            ->info('{type.name}')
    ]),



    $co->createColumn(5, [
        $co->createSection()
            ->section('heroArea')
            ->limit(2)
            ->info('{body}')
            ->imageField('image')
            ->actions(['relationships'])
            ->layout('cardlets'),

        $co->createSection()
            ->section('page')
            ->heading('Page Structure')
            ->info('{isDraft ? "Draft"} {type.name}')
            ->icon('@appicons/globe.svg')
            ->actions(['relationships','view'])
            ->scope('all')
    ]),


];
