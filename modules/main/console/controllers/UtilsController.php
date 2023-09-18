<?php

namespace modules\main\console\controllers;

use Craft;
use craft\console\Controller;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\errors\ElementNotFoundException;
use Throwable;
use yii\base\Exception;
use yii\console\ExitCode;
use yii\helpers\Console;
use const PHP_EOL;

/**
 * Utils controller
 *
 * One-off commands to fix things
 */
class UtilsController extends Controller
{
    /**
     * @param $actionID
     * @return array|string[]
     */
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

    /**
     * @return int
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
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

    /**
     * @return int
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws Throwable
     */
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
