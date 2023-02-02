<?php

namespace modules\main;

use Craft;
use craft\base\Element;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\helpers\ElementHelper;
use craft\services\Elements;
use modules\base\BaseModule;
use modules\main\behaviors\EntryBehavior;
use modules\main\conditions\HasDraftsConditionRule;
use modules\main\fields\EnvironmentVariableField;
use modules\main\fields\IncludeField;
use modules\main\fields\SiteField;
use modules\main\resources\CpAssetBundle;
use modules\main\services\ContentService;
use modules\main\twigextensions\TwigExtension;
use modules\main\validators\BodyContentValidator;
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

        $this->registerTranslationCategory();

        $this->registerBehaviors(Entry::class, [
            EntryBehavior::class
        ]);

        $this->registerFieldTypes([
            SiteField::class,
            EnvironmentVariableField::class,
            IncludeField::class
        ]);

        $this->registerTwigExtensions([
            TwigExtension::class
        ]);

        if (Craft::$app->request->isCpRequest) {
            $this->registerTemplateRoots(false, true);

            $this->registerConditionRuleTypes([
                HasDraftsConditionRule::class,
            ]);

            $this->registerWidgetTypes([
                MyProvisionalDraftsWidget::class,
            ]);

            $this->restrictSearchIndex();

            $this->validateAllSites();

            $this->createHooks();

            $this->registerAssetBundles([
                CpAssetBundle::class
            ]);

            $this->registerEntryValidators([
                [['bodyContent'], BodyContentValidator::class, 'on' => [Element::SCENARIO_LIVE]]
            ]);

        }
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
