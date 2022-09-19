<?php

namespace modules\main;

use Craft;
use craft\base\conditions\BaseCondition;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\events\RegisterConditionRuleTypesEvent;
use craft\helpers\ElementHelper;
use craft\services\Elements;
use modules\main\behaviors\EntryBehavior;
use modules\main\conditions\HasDraftsConditionRule;
use modules\main\conditions\HasEmptyAltTextConditionRule;
use modules\main\services\PdfService;
use yii\base\Event;
use yii\base\Module;


/**
 * @property PdfService $pdf
 */
class MainModule extends Module
{

    public function init(): void
    {
        // Required for php craft help
        Craft::setAlias('@modules/main', $this->getBasePath());

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\main\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\main\\controllers';
        }

        // Prevent password managers like Bitdefender Wallet from falsely inserting credentials into user form
        Craft::$app->view->hook('cp.users.edit.content', function(array &$context) {
            return '<input type="text" name="dummy-first-name" value="wtf" style="display: none">';
        });


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

        Event::on(
            BaseCondition::class,
            BaseCondition::EVENT_REGISTER_CONDITION_RULE_TYPES,
            function(RegisterConditionRuleTypesEvent $event) {
                $event->conditionRuleTypes = array_merge($event->conditionRuleTypes, [
                    HasEmptyAltTextConditionRule::class,
                    HasDraftsConditionRule::class,
                ]);
            }
        );

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


        parent::init();
    }
}
