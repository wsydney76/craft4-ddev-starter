<?php

namespace modules\main\twigextensions;

use Craft;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{

    public function getGlobals(): array
    {
        return  [
            'request' => Craft::$app->requestData
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

    public function quoteFilter(?string $text): string
    {
        return Craft::t('site', '“') . $text . Craft::t('site', '”');
    }

}
