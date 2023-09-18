<?php

namespace modules\main\widgets;

use Craft;
use craft\base\Widget;
use craft\elements\Entry;

class MyProvisionalDraftsWidget extends Widget
{
    /**
     * @inheritDoc
     */
    public static function displayName(): string
    {
        return Craft::t('main', 'My Open Edits');
    }

    /**
     * @inheritDoc
     */
    public static function icon(): ?string
    {
        return Craft::getAlias('@appicons/draft.svg');
    }

    /**
     * @inheritDoc
     */
    public function getBodyHtml(): ?string
    {
        $entries = Entry::find()
            ->drafts(true)
            ->provisionalDrafts(true)
            ->draftCreator(Craft::$app->user->identity)
            ->site('*')
            ->unique()
            ->status(null)
            ->orderBy('dateUpdated desc')
            ->all();

        return Craft::$app->view->renderTemplate('main/myprovisionaldrafts_widget', [
            'entries' => $entries,
        ]);
    }
}
