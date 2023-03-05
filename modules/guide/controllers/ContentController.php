<?php

namespace modules\guide\controllers;

use Craft;
use craft\web\Controller;
use craft\web\View;
use yii\web\Response;

/**
 * Content controller
 */
class ContentController extends Controller
{

    public function beforeAction($action): bool
    {
        $this->requirePermission('accesscp');
        return parent::beforeAction($action);
    }

    /**
     * guide/content action
     */
    public function actionShow(): Response
    {

        $template = Craft::$app->request->getRequiredBodyParam('template');

        return $this->asRaw($this->view->renderTemplate(
            "guide/_guide.twig",
            ['template' => $template],
            View::TEMPLATE_MODE_SITE));
    }
}
