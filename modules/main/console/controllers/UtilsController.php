<?php

namespace modules\main\console\controllers;

use Craft;
use craft\console\Controller;
use craft\elements\MatrixBlock;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Utils controller
 */
class UtilsController extends Controller
{

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'index':
                // $options[] = '...';
                break;
        }
        return $options;
    }

    /**
     * main/utils command
     */
    public function actionRepairMatrixHeadingTag(): int
    {
        $blocks = MatrixBlock::find()
            ->field('bodyContent')
            ->status(null)
            ->site('*')
            ->type('heading')
            ->htmlTag(':empty:')
            ->all();

        foreach ($blocks as $block) {
            Console::output('Repairing block ' . $block->id . ' in entry ' . $block->owner->title);
            $block->htmlTag = 'h2';
            Craft::$app->elements->saveElement($block);
        }

        return ExitCode::OK;
    }

    public function actionRepairMatrixAlign(): int
    {
        $blocks = MatrixBlock::find()
            ->field('bodyContent')
            ->status(null)
            ->type(['image', 'gallery'])
            ->align(':empty:')
            ->all();

        foreach ($blocks as $block) {
            Console::output('Repairing block ' . $block->id . ' in entry ' . $block->owner->title);
            $block->align = 'default';
            Craft::$app->elements->saveElement($block);
        }

        return ExitCode::OK;
    }
}
