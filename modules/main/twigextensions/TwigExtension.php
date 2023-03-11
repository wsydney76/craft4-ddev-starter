<?php

namespace modules\main\twigextensions;

use Craft;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{

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
