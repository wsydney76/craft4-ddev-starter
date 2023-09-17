<?php

namespace modules\main\elements\actions;

use Craft;
use craft\base\ElementAction;
use craft\base\ElementInterface;
use yii\base\Exception;

class CopyMarkdownLink extends ElementAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('site', 'Copy reference tag with markdown link');
    }

    /**
     * @inheritdoc
     */
    public function getTriggerHtml(): ?string
    {
        /** @var string|ElementInterface $elementType */
        /** @phpstan-var class-string<ElementInterface>|ElementInterface $elementType */
        $elementType = $this->elementType;

        if (($refHandle = $elementType::refHandle()) === null) {
            throw new Exception("Element type \"$elementType\" doesn't have a reference handle.");
        }

        Craft::$app->getView()->registerJsWithVars(fn($type, $refHandle) => <<<JS
(() => {
    new Craft.ElementActionTrigger({
        type: $type,
        bulk: false,
        activate: \$selectedItems => {
            Craft.ui.createCopyTextPrompt({
                label: Craft.t('app', 'Copy the reference tag'),
                value: '[LINKTEXT]({' + $refHandle + ':' + \$selectedItems.find('.element').data('id') + '})',
            });
        },
    });
})();
JS, [static::class, $refHandle]);

        return null;
    }
}
