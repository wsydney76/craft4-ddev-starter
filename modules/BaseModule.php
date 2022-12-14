<?php

namespace modules;

use Craft;
use craft\base\conditions\BaseCondition;
use craft\base\Model;
use craft\events\DefineBehaviorsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterConditionRuleTypesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\i18n\PhpMessageSource;
use craft\services\Dashboard;
use craft\services\Fields;
use craft\web\twig\variables\Cp;
use craft\web\View;
use yii\base\Event;
use yii\base\Module;
use function array_splice;


class BaseModule extends Module
{

    protected $handle = '';

    public function init()
    {
        $this->setAlias();

        $this->setControllerNamespace();

        parent::init();
    }

    protected function setAlias(): void
    {
        // Required for php craft help
        Craft::setAlias('@modules/' . $this->handle, $this->getBasePath());
    }

    protected function setControllerNamespace(): void
    {

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\' . $this->handle . '\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\' . $this->handle . '\\controllers';
        }
    }

    protected function registerTranslationCategory()
    {
        Craft::$app->i18n->translations[$this->handle] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => $this->basePath . '/translations',
            'allowOverrides' => true,
        ];
    }

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

    protected function registerTwigExtensions(array $extensions): void
    {
        foreach ($extensions as $extension) {
            Craft::$app->view->registerTwigExtension(Craft::createObject($extension));
        }
    }

    protected function registerServices(array $services): void
    {
        // Register Services
        $this->setComponents($services);
    }

    protected function registerNavItem(array $navItem, $pos = null)
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

    protected function registerAssetBundles(array $assetBundles)
    {
        foreach ($assetBundles as $assetBundle) {
            Craft::$app->view->registerAssetBundle($assetBundle);
        }
    }


}