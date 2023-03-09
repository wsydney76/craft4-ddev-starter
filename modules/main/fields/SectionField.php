<?php

namespace modules\main\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Cp;

class SectionField extends Field
{
    public static function displayName(): string
    {
        return 'Section';
    }

    public static function supportedTranslationMethods(): array
    {
        return [
            self::TRANSLATION_METHOD_NONE,
        ];
    }

    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        $sectionOptions = collect(Craft::$app->sections->getAllSections())
            ->map(fn($section) => [
                'label' => $section->name,
                'value' => $section->handle
            ])
           ;

        return Cp::selectizeHtml([
            'name' => $this->handle,
            'value' => $value,
            'options' => $sectionOptions
        ]);
    }
}