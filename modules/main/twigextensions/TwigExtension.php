<?php

namespace modules\main\twigextensions;

use Craft;
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

    public function getFunctions()
    {
        return [
            new TwigFunction('estimatedReadingTime', function ($blocks, $wpm = 200) {

                $totalWords = 0;

                foreach ($blocks as $block) {
                    $totalWords += str_word_count($block->text);
                }

                $minutes = floor($totalWords / $wpm);
                $seconds = floor($totalWords % $wpm / ($wpm / 60));

                return array(
                    'minutes' => $minutes,
                    'seconds' => $seconds
                );
            }),
        ];
    }

    public function quoteFilter(?string $text): string
    {
        return Craft::t('site', '“') . $text . Craft::t('site', '”');
    }

}
