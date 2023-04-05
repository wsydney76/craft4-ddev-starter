<?php

namespace modules\main\services;

use craft\base\Component;
use craft\elements\Entry;

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

}