<?php

namespace modules\main\behaviors;

use craft\elements\Entry;
use yii\base\Behavior;

class EntryBehavior extends Behavior
{
    public function ping()
    {
        return 'Pong';
    }

    public function getAuthors(): array
    {
        /** @var Entry $entry */
        $entry = $this->owner;

        $authors = [];

        if ($entry->persons) {
            $authors = $entry->persons->collect()
                ->map(fn(Entry $author) => [
                    'name' => $author->title,
                    'photo' => $author->photo->one()
                ])
                ->toArray();
        }

        if (!$authors) {
            $authors = [
                [
                    'name' => $entry->author->name,
                    'photo' => $entry->author->photo
                ]
            ];
        }

        return $authors;
    }

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
}