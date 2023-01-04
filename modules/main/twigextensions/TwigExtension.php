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

    public function getFunctions()
    {
        return [
            new TwigFunction('d', fn(mixed $var, int $depth = 20) => $this->dumpFunction($var, $depth)),

            new TwigFunction('dd', fn(mixed $var, int $depth = 20) => $this->dumpAndDieFunction($var, $depth)),
        ];
    }


    public function quoteFilter(?string $text): string
    {
        return Craft::t('site', '“') . $text . Craft::t('site', '”');
    }

    public function dumpFunction(mixed $var, int $depth)
    {
        $dumper = new \modules\main\helpers\HtmlDumper();
        $dumper->setTheme('light');
        $dumper->dump((new VarCloner())->cloneVar($var)->withMaxDepth($depth));
    }

    public function dumpAndDieFunction(mixed $var, int $depth) {
        while (ob_get_length() !== false) {
            // If ob_start() didn't have the PHP_OUTPUT_HANDLER_CLEANABLE flag, ob_get_clean() will cause a PHP notice
            // and return false.
            if (@ob_get_clean() === false) {
                break;
            }
        }

        $this->dumpFunction($var, $depth);

        exit();
    }

}
