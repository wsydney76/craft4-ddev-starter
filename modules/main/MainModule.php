<?php

namespace modules\main;

use Craft;
use craft\base\Element;
use craft\elements\actions\CopyReferenceTag;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\events\BlockTypesEvent;
use craft\events\ElementEvent;
use craft\events\RegisterElementSourcesEvent;
use craft\events\RegisterPreviewTargetsEvent;
use craft\fields\Matrix;
use craft\helpers\ArrayHelper;
use craft\helpers\ElementHelper;
use craft\models\VolumeFolder;
use craft\services\Elements;
use Illuminate\Support\Collection;
use modules\base\BaseModule;
use modules\main\behaviors\EntryBehavior;
use modules\main\conditions\HasDraftsConditionRule;
use modules\main\elements\actions\CopyMarkdownLink;
use modules\main\elements\actions\CopyReferenceLinkTag;
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
use function array_unshift;
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
            EntryBehavior::class,
        ]);

        $this->registerFieldTypes([
            SiteField::class,
            IncludeField::class,
            SectionField::class,
            // This field type ist currently not used, as it can only be used in global sets
            EnvironmentVariableField::class,
        ]);


        $this->registerCraftVariableServices([
            ['project', ProjectService::class],
        ]);

        $this->registerTwigExtensions([
            TwigExtension::class,
        ]);

        Collection::macro('addToCollection', function(string $key, mixed $value) {
            if ($this->has($key)) {
                /** @phpstan-ignore-next-line */
                $this->put($key, $this->get($key)->push($value));
            } else {
                /** @phpstan-ignore-next-line */
                $this->put($key, new Collection([$value]));
            }

            return $this;
        });

        if (!Craft::$app->request->isSiteRequest) {
            // Don't register only for CP requests, as project-config/rebuild would delete it from custom sources
            $this->registerConditionRuleTypes([
                HasDraftsConditionRule::class,
            ]);
        }

        if (Craft::$app->request->isCpRequest) {
            $this->registerTemplateRoots(false, true);

            $this->registerWidgetTypes([
                MyProvisionalDraftsWidget::class,
            ]);

            $this->restrictSearchIndex();

            $this->createHooks();

            $this->registerAssetBundles([
                CpAssetBundle::class,
            ]);

            $this->registerEntryValidators([
                [['bodyContent'], BodyContentValidator::class, 'on' => [Element::SCENARIO_LIVE]],
            ]);


            $this->registerElementActions(Entry::class, [
                // CopyReferenceTag::class,
                CopyReferenceLinkTag::class,
                CopyMarkdownLink::class,
            ]);

            $this->hideBlockTypes();

            $this->setElementIndexColumns();


            Event::on(
                Entry::class,
                Element::EVENT_REGISTER_PREVIEW_TARGETS,
                function(RegisterPreviewTargetsEvent $event) {

                    // There is no preview target if switching to Pro edition, so add one by default
                    if ($event->sender->getUrl() && !ArrayHelper::firstWhere($event->previewTargets, 'urlFormat', '{url}')) {
                        array_unshift($event->previewTargets, [
                            'label' => Craft::t('app', 'Primary {type} page', ['type' => Entry::lowerDisplayName()]),
                            'urlFormat' => '{url}',
                        ]);
                    }

                    // Add SEO preview target
                    $seoPreviewSections = Craft::$app->config->custom->seoPreviewSections ?? [];
                    if (in_array($event->sender->section->handle, $seoPreviewSections, true)) {
                        $event->previewTargets[] = [
                            'label' => Craft::t('site', 'SEO Preview'),
                            'urlFormat' => 'cp/preview-seo?id={id}&siteId={object.site.id}',
                        ];
                    }
                }
            );

            // https://github.com/craftcms/cms/discussions/13535
            if (Craft::$app->config->custom->showAssetFolders) {
                Event::on(Asset::class, Asset::EVENT_REGISTER_SOURCES, function(RegisterElementSourcesEvent $event) {
                    $assetsService = Craft::$app->getAssets();

                    foreach ($event->sources as &$source) {
                        if (isset($source['data']['folder-id'])) {
                            $folder = $assetsService->getFolderById($source['data']['folder-id']);
                            if ($folder && !$folder->parentId) {
                                $subfolders = $assetsService->getAllDescendantFolders($folder, withParent: false, asTree: true);
                                $source['nested'] = $this->folders2sources($subfolders, $source);
                            }
                        }
                    }
                });
            }

            $this->validateAllSites();
        }
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

                // Hide richText block type if CKEditor is not installed
                if (!Craft::$app->plugins->isPluginEnabled('ckeditor')) {
                    foreach ($event->blockTypes as $i => $blockType) {
                        if ($blockType->handle === 'richText') {
                            unset($event->blockTypes[$i]);
                        }
                    }
                }

                if (!Craft::$app->config->custom->allowYoutubeVideos) {
                    foreach ($event->blockTypes as $i => $blockType) {
                        if ($blockType->handle === 'youtubeVideo') {
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
                'height' => 70,
            ]);
        $this->setEntriesIndexImageColumn(
            'bigImage',
            'image',
            Craft::t('site', 'Image (big)'),
            [
                'width' => 120,
                'height' => 70,
            ]);
        $this->setEntriesIndexImageColumn(
            'bigPhoto',
            'photo',
            Craft::t('site', 'Photo (big)'),
            [
                'width' => 70,
                'height' => 70,
            ]);
    }

    protected function folders2sources(array $folders, array $rootSource): array
    {
        return array_map(fn(VolumeFolder $folder) => [
            'key' => "folder:$folder->uid",
            'label' => $folder->name,
            'hasThumbs' => true,
            'criteria' => ['folderId' => $folder->id],
            'defaultSort' => ['dateCreated', 'desc'],
            'data' => [
                'folder-id' => $folder->id,
                'can-upload' => $rootSource['data']['can-upload'] ?? false,
                'can-move-to' => $rootSource['data']['can-move-to'] ?? false,
                'can-move-peer-files-to' => $rootSource['data']['can-move-peer-files-to'] ?? false,
                'fs-type' => $rootSource['fs-type'] ?? null,
            ],
            'nested' => $this->folders2sources($folder->getChildren(), $rootSource),
        ], $folders);
    }

    private function validateAllSites()
    {
        if (!Craft::$app->config->custom->useCustomCrossSiteValidation) {
            return;
        }

        // Validate entries on all sites
        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_SAVE, function($event) {

            /** @var Entry $entry */
                $entry = $event->sender;

                // TODO: Check conditionals

                if ($entry->scenario !== Entry::SCENARIO_LIVE) {
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
                        $entry->type->hasTitleField ? 'title' : 'slug',
                        Craft::t('site', 'Error validating entry in') .
                        ' "' . $localizedEntry->site->name . '". ' .
                        implode(' ', $localizedEntry->getErrorSummary(false)));
                        $event->isValid = false;
                    }
                }
            });
    }
}
