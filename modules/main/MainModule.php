<?php

namespace modules\main;

use Craft;
use craft\base\Element;
use craft\elements\actions\CopyReferenceTag;
use craft\elements\Entry;
use craft\events\BlockTypesEvent;
use craft\events\ElementEvent;
use craft\fields\Matrix;
use craft\helpers\ElementHelper;
use craft\services\Elements;
use Illuminate\Support\Collection;
use modules\base\BaseModule;
use modules\main\behaviors\EntryBehavior;
use modules\main\conditions\HasDraftsConditionRule;
use modules\main\fields\EnvironmentVariableField;
use modules\main\fields\IncludeField;
use modules\main\fields\SectionField;
use modules\main\fields\SiteField;
use modules\main\resources\CpAssetBundle;
use modules\main\services\ProjectService;
use modules\main\twigextensions\TwigExtension;
use modules\main\validators\BodyContentValidator;
use modules\main\widgets\MyProvisionalDraftsWidget;
use yii\base\Event;
use function in_array;


class MainModule extends BaseModule
{

    public $handle = 'main';

    public function init(): void
    {

        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)


        $this->registerTranslationCategory();

        $this->registerBehaviors(Entry::class, [
            EntryBehavior::class
        ]);

        $this->registerFieldTypes([
            SiteField::class,
            EnvironmentVariableField::class,
            IncludeField::class,
            SectionField::class
        ]);


        $this->registerCraftVariableServices([
            ['project', ProjectService::class]
        ]);

        $this->registerTwigExtensions([
            TwigExtension::class
        ]);

        Collection::macro('addToList', function(string $key, mixed $value) {
            if ($this->has($key)) {
                $this->put($key, $this->get($key)->push($value));
            } else {
                $this->put($key, collect([$value]));
            }

            return $this;
        });

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


            $this->registerElementActions(Entry::class, [
                CopyReferenceTag::class
            ]);

            $this->hideBlockTypes();

            $this->setElementIndexColumns();
        }
    }

    protected function validateAllSites()
    {
        // Validate entries on all sites (fixes open Craft bug)
        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_SAVE, function($event): void {

            if (Craft::$app->sites->getTotalSites() === 1) {
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


        // Let users reset their dismissed tips/warnings
        // This will only be called for users editing their own profile
        Craft::$app->view->hook('cp.users.edit.prefs', function(array &$context) {
            return Craft::$app->view->renderTemplate('main/cp-dismissed-tips.twig');
        });
    }

    protected function hideBlockTypes()
    {
        // Hide bodyContent block types not relevant for the current entry
        Event::on(
            Matrix::class,
            Matrix::EVENT_SET_FIELD_BLOCK_TYPES,
            function(BlockTypesEvent $event) {

                // Only hide block types for bodyContent field
                if (!$event->element instanceof Entry || $event->sender->handle !== 'bodyContent') {
                    return;
                }

                $entry = $event->element;

                // TODO: Make that configurable
                // Hide dynamicBlock and contentComponents block types for pages
                if ($entry->section->handle !== 'page' || in_array($entry->type->handle, ['faqs', 'sitemap'])) {
                    foreach ($event->blockTypes as $i => $blockType) {
                        if (in_array($blockType->handle, ['dynamicBlock', 'contentComponents'])) {
                            unset($event->blockTypes[$i]);
                        }
                    }
                }

                // Hide richText block type if Redactor is not installed
                if (!Craft::$app->plugins->isPluginEnabled('redactor')) {
                    foreach ($event->blockTypes as $i => $blockType) {
                        if ($blockType->handle === 'richText') {
                            unset($event->blockTypes[$i]);
                        }
                    }
                }
            });
    }


    protected function setElementIndexColumns()
    {
        $this->setEntriesIndexImageColumn(
            'bigFeaturedImage',
            'featuredImage',
            Craft::t('site', 'Featured Image (big)'),
            [
                'width' => 120,
                'height' => 70
            ]);
        $this->setEntriesIndexImageColumn(
            'bigImage',
            'image',
            Craft::t('site', 'Image (big)'),
            [
                'width' => 120,
                'height' => 70
            ]);
        $this->setEntriesIndexImageColumn(
            'bigPhoto',
            'photo',
            Craft::t('site', 'Photo (big)'),
            [
                'width' => 70,
                'height' => 70
            ]);
    }


}
