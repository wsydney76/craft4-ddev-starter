<?php

namespace modules\main\models;

use wsydney76\contentoverview\models\Section;

/**
 * Sample model for Content Overview Plugin.
 */

class NewsSection extends Section
{
    public array|string $section = 'news';
    public ?int $limit = 12;
    public array|string $imageField = 'featuredImage';
    public ?string $layout = 'cardlets';
    public bool $buttons = false;
    public array|string $info = '{tagline}, {postDate|date("short")}';
}