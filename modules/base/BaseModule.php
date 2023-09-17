<?php

namespace modules\base;

use Craft;
use craft\base\conditions\BaseCondition;
use craft\base\Element;
use craft\base\Field;
use craft\base\Model;
use craft\elements\Entry;
use craft\events\DefineBehaviorsEvent;
use craft\events\DefineRulesEvent;
use craft\events\ElementIndexTableAttributeEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterConditionRuleTypesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterElementActionsEvent;
use craft\events\RegisterElementTableAttributesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\SetElementTableAttributeHtmlEvent;
use craft\helpers\Html;
use craft\i18n\PhpMessageSource;
use craft\services\Dashboard;
use craft\services\Fields;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use modules\base\services\ContentService;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Module;
use function array_splice;

/**
 * @property-read ContentService $contentService
 */
class BaseModule extends Module
{
    protected string $handle = '';

    public function init(): void
    {
        $this->setAlias();

        $this->setControllerNamespace();

        $this->setComponents([
            'contentService' => ContentService::class,
        ]);

        parent::init();
    }

    /**
     * @return void
     */
    protected function setAlias(): void
    {
        // Required for php craft help
        Craft::setAlias('@modules/' . $this->handle, $this->getBasePath());
    }

    /**
     * @return void
     */
    protected function setControllerNamespace(): void
    {

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\' . $this->handle . '\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\' . $this->handle . '\\controllers';
        }
    }

    /**
     * @return void
     */
    protected function registerTranslationCategory(): void
    {
        Craft::$app->i18n->translations[$this->handle] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => $this->basePath . '/translations',
            'allowOverrides' => true,
        ];
    }

    /**
     * @param bool $site
     * @param bool $cp
     * @return void
     */
    protected function registerTemplateRoots(bool $site = true, bool $cp = true): void
    {
        // Base template directory
        if ($site) {
            Event::on(
                View::class,
                View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $event): void {
                    $event->roots[$this->handle] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates';
                });
        }

        if ($cp) {
            Event::on(
                View::class,
                View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $event): void {
                    $event->roots[$this->handle] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates';
                });
        }
    }

    /**
     * @param string $className
     * @param array<string> $behaviors
     * @return void
     */
    protected function registerBehaviors(string $className, array $behaviors): void
    {
        // Register Behaviors
        Event::on(
            $className,
            Model::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) use ($behaviors): void {
                foreach ($behaviors as $behavior) {
                    $event->behaviors[] = $behavior;
                }
            });
    }

    /**
     * @param array<string> $conditionRuleTypes
     * @return void
     */
    protected function registerConditionRuleTypes(array $conditionRuleTypes): void
    {
        // Register Custom Conditions
        Event::on(
            BaseCondition::class,
            BaseCondition::EVENT_REGISTER_CONDITION_RULE_TYPES,
            function(RegisterConditionRuleTypesEvent $event) use ($conditionRuleTypes): void {
                foreach ($conditionRuleTypes as $conditionRuleType) {
                    $event->conditionRuleTypes[] = $conditionRuleType;
                }
            }
        );
    }

    /**
     * @param array<string> $fieldTypes
     * @return void
     */
    protected function registerFieldTypes(array $fieldTypes): void
    {
        // Register custom field types
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) use ($fieldTypes): void {
                foreach ($fieldTypes as $fieldType) {
                    $event->types[] = $fieldType;
                }
            });
    }

    /**
     * @param array<string> $widgetTypes
     * @return void
     */
    protected function registerWidgetTypes(array $widgetTypes): void
    {
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function(RegisterComponentTypesEvent $event) use ($widgetTypes) {
                foreach ($widgetTypes as $widgetType) {
                    $event->types[] = $widgetType;
                }
            }
        );
    }

    /**
     * @param array<string> $extensions
     * @return void
     * @throws InvalidConfigException
     */
    protected function registerTwigExtensions(array $extensions): void
    {
        foreach ($extensions as $extension) {
            /* @phpstan-ignore-next-line */
            Craft::$app->view->registerTwigExtension(Craft::createObject($extension));
        }
    }

    /**
     * @param array<string> $services
     * @return void
     */
    protected function registerServices(array $services): void
    {
        // Register Services
        $this->setComponents($services);
    }

    /**
     * @param array $navItem
     * @param $pos
     * @return void
     */
    protected function registerNavItem(array $navItem, ?int $pos = null): void
    {
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) use ($navItem, $pos) {
                if ($pos) {
                    array_splice($event->navItems, $pos, 0, [$navItem]);
                } else {
                    $event->navItems[] = $navItem;
                }
            }
        );
    }

    /**
     * @param array<string> $assetBundles
     * @return void
     * @throws InvalidConfigException
     */
    protected function registerAssetBundles(array $assetBundles): void
    {
        foreach ($assetBundles as $assetBundle) {
            Craft::$app->view->registerAssetBundle($assetBundle);
        }
    }

    /**
     * @param array $rules
     * @return void
     */
    protected function registerEntryValidators(array $rules): void
    {
        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_RULES, function(DefineRulesEvent $event) use ($rules) {
                foreach ($rules as $rule) {
                    $event->rules[] = $rule;
                }
            });
    }

    /**
     * @param string $elementType
     * @param array<string> $actions
     * @return void
     */
    protected function registerElementActions(string $elementType, array $actions): void
    {
        Event::on(
            $elementType,
            Element::EVENT_REGISTER_ACTIONS,
            function(RegisterElementActionsEvent $event) use ($actions) {
                foreach ($actions as $action) {
                    $event->actions[] = $action;
                }
            }
        );
    }

    /**
     * @param array $services
     * @return void
     */
    protected function registerCraftVariableServices(array $services): void
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function($event) use ($services) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                foreach ($services as $service) {
                    $variable->set($service[0], $service[1]);
                }
            }
        );
    }

    /**
     * @param array<string> $behaviors
     * @return void
     */
    protected function registerCraftVariableBehaviors(array $behaviors): void
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function($event) use ($behaviors) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->attachBehaviors($behaviors);
            }
        );
    }


    /**
     * Define a new column for the entries index, which will display a bigger image
     *
     * @param string $attribute handle of the new column
     * @param string $fieldHandle handle of the field to use for the image
     * @param string $label label of the new column
     * @param array $transform transform to use for the image
     */
    protected function setEntriesIndexImageColumn(string $attribute, string $fieldHandle, string $label, array $transform): void
    {
        // Register table attribute
        Event::on(
            Entry::class,
            Element::EVENT_REGISTER_TABLE_ATTRIBUTES,
            function(RegisterElementTableAttributesEvent $event) use ($attribute, $label) {
                $event->tableAttributes[$attribute] = ['label' => $label];
            });

        // Set element index column content
        Event::on(
            Entry::class,
            Element::EVENT_SET_TABLE_ATTRIBUTE_HTML,
            function(SetElementTableAttributeHtmlEvent $event) use ($attribute, $fieldHandle, $transform) {
                if ($event->attribute === $attribute) {
                    /** @var Entry $entry */
                    $entry = $event->sender;

                    // Set default html
                    $event->html = '';

                    // Get the image fields query
                    $query = $entry->getFieldValue($fieldHandle);

                    // If the field is in the entries field layout
                    if ($query) {
                        $image = $query->one();
                        if ($image) {
                            $image->setTransform($transform);
                            $event->html = Html::tag('img', '', [
                                'src' => $image->url,
                                'style' => 'border-radius: 3px;',
                                'width' => $image->width,
                                'height' => $image->height,
                                'alt' => $image->altText ?? $image->title,
                                'ondblclick' => "Craft.createElementEditor('craft\\\\elements\\\\Asset', {elementId: {$image->id}, siteId: {$entry->site->id}})",
                            ]);
                        }
                    }

                    // Prevent further processing
                    $event->handled = true;
                }
            });

        // Eager load transformed images
        Event::on(
            Entry::class,
            Entry::EVENT_PREP_QUERY_FOR_TABLE_ATTRIBUTE,
            function(ElementIndexTableAttributeEvent $event) use ($attribute, $fieldHandle, $transform) {
                if ($event->attribute === $attribute) {
                    // Eager load the image element including the transform
                    $event->query->andWith(
                        [$fieldHandle, ['withTransforms' => [$transform]]]
                    );
                }
            });
    }
}
