<?php

namespace modules\main\console\controllers;

use Craft;
use craft\console\Controller;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use yii\console\ExitCode;
use yii\helpers\Console;
use const PHP_EOL;

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
        /* @phpstan-ignore-next-line */
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

    // Do not allow empty values
    public function actionRepairMatrixAlign(): int
    {
        /* @phpstan-ignore-next-line */
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

    // That was a bad idea, so revert it...
    public function actionRepairDefault(): int
    {
        // $elements = Entry::find()

        /* @phpstan-ignore-next-line */
        $elements = MatrixBlock::find()
            ->field('bodyContent')
            ->type('gallery')
            ->align('default')
            ->all();

        foreach ($elements as $element) {
            $this->stdout($element->id);

            $element->setFieldValue('align', '');


            if (!Craft::$app->elements->saveElement($element)) {
                $this->stdout(' Error ');
                Craft::dump($element->errors);
            }

            $this->stdout(PHP_EOL);
        }

        return ExitCode::OK;
    }
}
