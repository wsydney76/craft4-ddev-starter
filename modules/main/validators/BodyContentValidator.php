<?php

namespace modules\main\validators;

use Craft;
use craft\elements\MatrixBlock;
use yii\validators\Validator;

class BodyContentValidator extends Validator

// https://nystudio107.com/blog/custom-matrix-block-validation-rules
{
    /**
     * @inerhitdoc
     */
    public function validateAttribute($model, $attribute): void
    {
        $isHeadingLevelValid = true;
        $lastHeadingLevel = 1;

        $query = $model->$attribute;
        // Iterate through all the blocks
        $blocks = $query->getCachedResult() ?? $query->limit(null)->anyStatus()->all();

        /** @var MatrixBlock $block */
        foreach ($blocks as $index => $block) {
            if ($block->type->handle === 'heading') {
                /* @phpstan-ignore-next-line */
                $level = (int)str_replace('h', '', $block->htmlTag->value);
                if ($level - $lastHeadingLevel > 1) {
                    $model->addError('bodyContent', Craft::t('site', 'Block {index}: H{level} cannot follow H{lastHeadingLevel}.', [
                        'level' => $level,
                        'lastHeadingLevel' => $lastHeadingLevel,
                        'index' => $index + 1,
                    ]));
                    $isHeadingLevelValid = false;
                }
                $lastHeadingLevel = $level;
            }
        }

        $query->setCachedResult($blocks);

        if (!$isHeadingLevelValid) {
            $model->addError('bodyContent', Craft::t('site', 'Nesting of heading levels is wrong.'));
        }
    }
}
