<?php
/** @var wsydney76\contentoverview\services\ContentOverviewService $co */

return [
    $co->createColumn(12, [
        $co->createSection()
            ->section('person')
            ->orderBy('title')
            ->heading('Persons')
            ->info('{teaser}<br>{postDate|date("short")}')
            ->imageField('photo')
            ->layout('cards')
            ->size('tiny')
            ->imageRatio(4/5)
            ->actions(['relationships'])
            ->limit(12),

    ]),





];
