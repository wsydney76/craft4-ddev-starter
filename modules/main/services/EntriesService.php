<?php

namespace modules\main\services;

use Craft;
use craft\elements\Entry;
use craft\helpers\App;
use DateTime;
use yii\base\Component;
use function in_array;

/**
 * Entries Service service
 */
class EntriesService extends Component
{

    /**
     * Update last modified of frontpages for sitemap
     *
     * TODO: Make configurable, add contentComponents, use background job
     *
     * @return void
     */
    public function updateFrontPages(Entry $entry): void
    {
        if (App::devMode()) {
            return;
        }

        $watchSections = ['news'];

        $types = ['home', 'newsIndex'];
        if (in_array($entry->section->handle, $watchSections, true)) {
            foreach ($types as $type) {
                $frontPage = Entry::find()
                    ->section('page')
                    ->type($type)
                    ->one();

                if ($frontPage) {
                    $frontPage->dateUpdated = new DateTime();
                    if (!Craft::$app->elements->saveElement($frontPage)) {
                        Craft::warning('Could not update frontpage');
                    }
                }
            }
        }
    }
}
