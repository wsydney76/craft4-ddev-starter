<?php

namespace modules\main\behaviors;

use craft\elements\Entry;
use yii\base\Behavior;

/**
 * Class EntryBehavior
 *
 * Provides additional behavior to the Entry model.
 *
 * @package modules\main\behaviors
 */
class EntryBehavior extends Behavior
{
    /**
     * Ping function for testing.
     *
     * @return string Always returns 'Pong'
     */
    public function ping()
    {
        return 'Pong';
    }

    /**
     * Fetch authors associated with an entry.
     *
     * This function will first attempt to fetch all authors from the 'persons' field.
     * If no authors are found, it will return the author directly associated with the entry.
     *
     * @return array An array of authors, each represented as an array with 'name', 'photo' and 'socialLinks'
     */
    public function getAuthors(): array
    {
        /** @var Entry $entry */
        $entry = $this->owner;

        $authors = [];

        // Attempt to get authors from 'persons' field
        if ($entry->persons) {
            $authors = $entry->persons->collect()
                ->map(fn(Entry $author) => [
                    'name' => $author->title,
                    'photo' => $author->photo->one(),
                    'socialLinks' => $author->socialLinks,
                ])
                ->toArray();
        }

        // If no authors found in 'persons', get the author directly associated with the entry
        if (!$authors) {
            $authors = [
                [
                    'name' => $entry->author->name,
                    'photo' => $entry->author->photo,
                    'socialLinks' => $entry->author->socialLinks,
                ],
            ];
        }

        return $authors;
    }

    /**
     * Checks if the sticky menu should be enforced for the entry.
     *
     * @return bool Returns true if the entry's type handle is 'story', false otherwise
     */
    public function forceStickyMenu(): bool
    {
        /** @var Entry $entry */
        $entry = $this->owner;

        // Check if entry type handle is 'story'
        return $entry->type->handle === 'story';
    }
}
