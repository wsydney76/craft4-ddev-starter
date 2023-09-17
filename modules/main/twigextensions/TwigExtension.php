<?php

namespace modules\main\twigextensions;

use Craft;
use craft\helpers\HtmlPurifier;
use craft\helpers\Json;
use craft\helpers\Template;
use modules\main\services\ProjectService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use yii\base\InvalidArgumentException;
use yii\helpers\Markdown;
use function file_get_contents;
use function is_file;
use const DIRECTORY_SEPARATOR;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    private ?array $purifierConfig = null;

    public function getGlobals(): array
    {
        return [
            'customConfig' => Craft::$app->getConfig()->custom,
        ];
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('quotationMarks', fn(?string $text): string => $this->quotationMarksFilter($text)),
            new TwigFilter('prepareText', fn(?string $text): string => $this->prepareTextFilter($text), ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('estimatedReadingTime', function($blocks, $wpm = 200) {
                return ProjectService::estimatedReadingTime($blocks, $wpm);
            }),
        ];
    }

    public function quotationMarksFilter(?string $text): string
    {
        // return Template::raw(Html::tag('q', $text));
        return Craft::t('site', '“') . $text . Craft::t('site', '”');
    }

    public function prepareTextFilter(?string $text): string
    {
        $customConfig = Craft::$app->getConfig()->custom;

        // 1. Markdown
        $text = Markdown::process($text, $customConfig->markdownFlavor ?? 'original');

        // 2. ParseRefs
        $text = Craft::$app->getElements()->parseRefs($text);

        // 3. Purify

        if (!$this->purifierConfig) {
            $path = Craft::$app->getPath()->getConfigPath() . DIRECTORY_SEPARATOR .
                'htmlpurifier' . DIRECTORY_SEPARATOR .
                ($customConfig->purifierConfig ?? 'Default') . '.json';

            $config = null;
            if (!is_file($path)) {
                Craft::warning("No HTML Purifier config found at $path.");
            } else {
                try {
                    $this->purifierConfig = Json::decode(file_get_contents($path));
                } catch (InvalidArgumentException) {
                    Craft::warning("Invalid HTML Purifier config at $path.");
                }
            }
        }

        $text = HtmlPurifier::process($text, $this->purifierConfig);

        // 4. Unify accents

        $accents = $customConfig->accents ?? '';

        if ($accents === 'strong') {
            $text = str_replace(
                ['<em>', '</em>', '<i>', '</i>'],
                ['<strong>', '</strong>', '<strong>', '</strong>'],
                $text);
        } elseif ($accents === 'italic') {
            $text = str_replace(
                ['<strong>', '</strong>', '<b>', '</b>'],
                ['<em>', '</em>', '<em>', '</em>'],
                $text);
        }


        return Template::raw($text);
    }
}
