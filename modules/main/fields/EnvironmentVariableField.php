<?php

namespace modules\main\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\errors\InvalidFieldException;
use craft\helpers\App;
use craft\helpers\ArrayHelper;
use craft\helpers\Cp;
use yii\base\Exception;
use function array_merge;

/**
 * Field type for updating .env file variables from the CP
 *
 * Note: This field type should only be used in global sets, as it does not support drafts.
 *
 *
 * @property-read array $elementValidationRule
 * @property-read array $elementValidationRules
 * @property-read null|string $settingsHtml
 */
class EnvironmentVariableField extends Field
{
    /**
     * The Variable's key in .env file
     */
    public string $variableName = '';

    /**
     * The YII core validation rule name to be applied for the field instance
     */
    public string $validationRule = '';

    /**
     * Validation rule options
     * No defaults containing dynamic values allowed here, so we set values in init() method
     * For now, value can only be the name of a YII/Craft core validator that does not need additional parameters.
     * https://www.yiiframework.com/doc/guide/2.0/en/tutorial-core-validators
     */
    protected array $validationRules = [];

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->validationRules = [
            [
                'label' => Craft::t('site', 'No validation'),
                'value' => '',
            ],
            [
                'label' => Craft::t('site', 'Email'),
                'value' => 'email',
            ],
            [
                'label' => Craft::t('site', 'Integer'),
                'value' => 'integer',
            ],
            [
                'label' => Craft::t('site', 'Boolean (0/1)'),
                'value' => 'boolean',
            ],
            [
                'label' => Craft::t('site', 'URL'),
                'value' => 'url',
            ],
        ];
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('site', 'Environment Variable');
    }

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }

    /**
     * @return array
     */
    public static function supportedTranslationMethods(): array
    {
        return [self::TRANSLATION_METHOD_NONE];
    }

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['variableName', 'trim'],
            ['variableName', 'required'],
            ['variableName', 'match', 'pattern' => '/^[A-Z_]*$/'],
            ['validationRule', 'in', 'range' => ArrayHelper::getColumn($this->validationRules, 'value')],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        // If an element is currently being saved, the value will be the field’s POST data. Otherwise, null, so get value from .env
        return $value !== null ?
            trim($value) :
            App::env($this->variableName);
    }

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        $rules = parent::getElementValidationRules();

        if ($this->validationRule) {
            $rules[] = [$this->handle, $this->validationRule, 'on' => Element::SCENARIO_LIVE];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     * @throws InvalidFieldException
     * @throws Exception
     */
    public function afterElementSave(ElementInterface $element, bool $isNew): void
    {
        $oldValue = App::env($this->handle);
        $newValue = $element->getFieldValue($this->handle);

        if ($oldValue !== $newValue) {
            Craft::$app->config->setDotEnvVar($this->variableName, $newValue);
        }
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Cp::textFieldHtml([
                'label' => Craft::t('site', 'Variable Name'),
                'instructions' => Craft::t('site', 'Name of the variable in .env file. Only uppercase letters and underscore allowed [A-Z_]'),
                'name' => 'variableName',
                'value' => $this->variableName,
                'required' => true,
                'errors' => $this->getErrors('variableName'),
            ]) .

            Cp::selectFieldHtml([
                'label' => Craft::t('site', 'Validation Rule'),
                'instructions' => Craft::t('site', 'How a user input should be validated'),
                'name' => 'validationRule',
                'value' => $this->validationRule,
                'errors' => $this->getErrors('validationRule'),
                'options' => $this->validationRules,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        return Cp::textFieldHtml([
            'name' => $this->handle,
            'value' => $value,
        ]);
    }
}
