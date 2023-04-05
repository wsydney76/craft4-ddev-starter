<?php

namespace modules\main\twigextensions;

use Craft;
use Illuminate\Support\Collection;
use modules\main\MainModule;
use modules\main\services\ProjectService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function floor;
use function str_word_count;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{

    public function getGlobals(): array
    {
        return  [
            '_globals' => Collection::make(),
            'customConfig' => Craft::$app->getConfig()->custom,
        ];
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('quote', fn(?string $text): string => $this->quoteFilter($text))
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('estimatedReadingTime', function ($blocks, $wpm = 200) {
                return ProjectService::estimatedReadingTime($blocks, $wpm);
            }),
        ];
    }

    public function quoteFilter(?string $text): string
    {
        return Craft::t('site', '“') . $text . Craft::t('site', '”');
    }

}
