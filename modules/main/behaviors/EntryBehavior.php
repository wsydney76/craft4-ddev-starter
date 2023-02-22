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
                    'photo' => $author->photo->one(),
                    'socialLinks' => $author->socialLinks
                ])
                ->toArray();
        }

        if (!$authors) {
            $authors = [
                [
                    'name' => $entry->author->name,
                    'photo' => $entry->author->photo,
                    'socialLinks' => $entry->author->socialLinks
                ]
            ];
        }

        return $authors;
    }
}