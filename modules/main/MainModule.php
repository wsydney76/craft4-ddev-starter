<?php

namespace modules\main;

use Craft;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\helpers\ElementHelper;
use craft\services\Elements;
use modules\BaseModule;
use modules\main\behaviors\EntryBehavior;
use modules\main\conditions\HasDraftsConditionRule;
use modules\main\conditions\HasEmptyAltTextConditionRule;
use modules\main\fields\SiteField;
use modules\main\services\ContentService;
use modules\main\twigextensions\TwigExtension;
use modules\main\widgets\MyProvisionalDraftsWidget;
use yii\base\Event;


/**
 * @property ContentService $content
 */

class MainModule extends BaseModule
{

    public $handle = 'main';

    public function init(): void
    {

        parent::init();

        $this->registerServices([
            'content' => ContentService::class
        ]);

        $this->registerTemplateRoots(false, true);

        $this->registerTranslationCategory();

        $this->registerBehaviors(Entry::class, [
            EntryBehavior::class
        ]);

        $this->registerConditionRuleTypes([
            HasEmptyAltTextConditionRule::class,
            HasDraftsConditionRule::class,
        ]);

        $this->registerFieldTypes([
            SiteField::class
        ]);

        $this->registerWidgetTypes([
            MyProvisionalDraftsWidget::class
        ]);

        $this->registerTwigExtensions([
            TwigExtension::class
        ]);

        $this->restrictSearchIndex();

        $this->validateAllSites();

        $this->createHooks();


    }

    protected function validateAllSites()
    {
        // Validate entries on all sites (fixes open Craft bug)
        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_SAVE, function($event): void {

            if (Craft::$app->sites->getTotalSites() == 1) {
                return;
            }

            /** @var Entry $entry */
            $entry = $event->sender;

            // TODO: Check conditionals

            if ($entry->resaving || $entry->propagating || $entry->getScenario() != Entry::STATUS_LIVE) {
                return;
            }

            $entry->validate();

            if ($entry->hasErrors()) {
                return;
            }

            foreach ($entry->getLocalized()->all() as $localizedEntry) {
                $localizedEntry->scenario = Entry::SCENARIO_LIVE;

                if (!$localizedEntry->validate()) {
                    $entry->addError(
                        $entry->getType()->hasTitleField ? 'title' : 'slug',
                        Craft::t('site', 'Error validating entry in') .
                        ' "' . $localizedEntry->site->name . '". ' .
                        implode(' ', $localizedEntry->getErrorSummary(false)));
                    $event->isValid = false;
                }
            }
        });
    }


    protected function restrictSearchIndex()
    {
        // Don't update search index for drafts
        Event::on(
            Elements::class,
            Elements::EVENT_BEFORE_UPDATE_SEARCH_INDEX,
            function(ElementEvent $event) {
                if (ElementHelper::isDraftOrRevision($event->element)) {
                    $event->isValid = false;
                }
            }
        );
    }

    protected function createHooks()
    {
        // Prevent password managers like Bitdefender Wallet from falsely inserting credentials into user form
        Craft::$app->view->hook('cp.users.edit.content', function(array &$context) {
            return '<input type="text" name="dummy-first-name" value="wtf" style="display: none">';
        });
    }
}
