<?php

namespace modules\main\fields;

use Craft;
use craft\base\Field;
use craft\base\FieldInterface;
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

    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
    {
        $sites = collect(Craft::$app->sites->getAllSites());
        $siteOptions = $sites->map(
          fn($site) => [
              'label' => $site->name,
              'value' => $site->handle
          ]
        );

        return Cp::selectHtml([
            'name' => $this->handle,
            'value' => $value,
            'options' =>  $siteOptions->prepend([
                'label' => Craft::t('main', 'All Sites'),
                'value' => ''
            ])
        ]);
    }
}