<?php

namespace modules\main\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Cp;

class SiteField extends Field
{
    public static function displayName(): string
    {
        return 'Site';
    }

    public static function supportedTranslationMethods(): array
    {
        return [
            self::TRANSLATION_METHOD_NONE,
        ];
    }

    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        $siteOptions = collect(Craft::$app->sites->getAllSites())
            ->map(fn($site) => [
                'label' => $site->name,
                'value' => $site->handle,
            ])
            ->prepend([
                'label' => Craft::t('main', 'All Sites'),
                'value' => '',
            ]);

        return Cp::selectizeHtml([
            'name' => $this->handle,
            'value' => $value,
            'options' => $siteOptions,
            'selectizeOptions' => [
                'allowEmptyOption' => true,
            ],
        ]);
    }
}
