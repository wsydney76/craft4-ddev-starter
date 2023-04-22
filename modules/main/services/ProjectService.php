<?php

namespace modules\main\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\helpers\HtmlPurifier;
use craft\helpers\Template;
use function preg_match_all;
use function str_replace;
use const PREG_PATTERN_ORDER;

class ProjectService extends Component
{
    /**
     * Retrieve taxonomy hierarchy with eager loaded images
     *
     * Performance optimization, minimize database query count and make it easy to use in Twig.
     *
     * @return array
     */
    public function getStructureNodes(string $sectionHandle)
    {
        $entries = Entry::find()
            ->section($sectionHandle)
            ->with('featuredImage')
            ->collect();

        $topics = [];

        foreach ($entries as $entry) {
            switch ($entry->level) {
                case 1:
                {
                    $topics[$entry->id] = ['entry' => $entry, 'children' => []];
                    $last1 = $entry->id;
                    break;
                }
                case 2:
                {
                    $topics[$last1]['children'][$entry->id] = ['entry' => $entry, 'children' => []];
                    $last2 = $entry->id;
                    break;
                }
                case 3:
                {
                    $topics[$last1]['children'][$last2]['children'][$entry->id] = ['entry' => $entry];
                }
            }
        }

        return $topics;
    }

    public static function estimatedReadingTime($blocks, $wpm = 200)
    {

        $totalWords = 0;

        foreach ($blocks as $block) {
            $totalWords += str_word_count($block->text);
        }

        $minutes = floor($totalWords / $wpm);
        $seconds = floor($totalWords % $wpm / ($wpm / 60));

        return [
            'minutes' => $minutes,
            'seconds' => $seconds
        ];
    }

    /**
     * Handle Oembed tags for YouTube videos
     *
     * @param $text
     * @return \Twig\Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    function replaceOembedTags($text)
    {

        // run text through htmlpurifier to remove any malicious code
        $text = HtmlPurifier::process($text) ;

        // use a regular expression to match the <oembed> tags and extract the video key
        // TODO: Check whether possible variations (spaces, tabs, line breaks, etc.) are correctly taken into account.
        $pattern = '/<oembed\s?url\s*=\s*"https:\/\/(www\.)?youtube\.[a-z]+\/watch\?v=([a-zA-Z0-9_-]+)">\s*<\/oembed>/';

        preg_match_all($pattern, $text, $matches, PREG_PATTERN_ORDER);

        // loop through each match and replace the <oembed> tag with the video block template
        foreach ($matches[2] as $i => $key) {

            $search = $matches[0][$i];

            $replacement = Craft::$app->getView()->renderTemplate('_blocks/youtubeVideo.twig', [
                'block' => [
                    'key' => $key,
                    'heading' => '',
                    'text' => '',
                    'aspectRatio' => ['value' => '16:9'],
                ]
            ]);

            $text = str_replace($search, $replacement, $text);
        }

        // return the modified text
        return Template::raw($text);
    }

}