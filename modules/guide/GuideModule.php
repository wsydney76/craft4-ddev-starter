<?php

namespace modules\guide;

use Craft;
use craft\elements\Entry;
use craft\events\DefineHtmlEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\App;
use craft\web\twig\variables\Cp;
use craft\web\View;
use modules\base\BaseModule;
use yii\base\Event;
use yii\base\Module;
use const DIRECTORY_SEPARATOR;

/**
 * guide module
 *
 * @method static Module getInstance()
 */
class GuideModule extends BaseModule
{
    public string $handle = 'guide';

    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $event): void {
                $event->roots['guide'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates';
                // Enable twig to use templates available only in 'site' template mode
                $event->roots['_guides'] = App::parseEnv('@templates/_guides');
                $event->roots['_layouts'] = App::parseEnv('@templates/_layouts');
            });

        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_SIDEBAR_HTML, function(DefineHtmlEvent $event) {
                $event->html .= Craft::$app->view->renderTemplate('guide/editorbutton.twig', ['entry' => $event->sender]);
            });

        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) {
                $event->navItems[] = [
                  'url' => 'guide',
                    'label' => 'Guide',
                    'icon' => '@appicons/routes.svg',
                ];
            }
        );
    }
}
