<?php

namespace modules\main\fields;

use Craft;
use craft\base\Field;
use craft\elements\Entry;
use craft\helpers\App;
use craft\helpers\Cp;
use Exception;
use function is_dir;
use function str_contains;
use function str_replace;
use function ucwords;
use const DIRECTORY_SEPARATOR;

class IncludeField extends Field
{

    public string $includeDirectory = '';

    /**
     * @inheritDoc
     */
    public static function displayName(): string
    {
        return 'Include Template';
    }

    public function getHandle(): ?string
    {
        return $this->handle;
    }

    /**
     * @inheritDoc
     */
    public static function supportedTranslationMethods(): array
    {
        return [
            self::TRANSLATION_METHOD_NONE,
            self::TRANSLATION_METHOD_SITE,
            self::TRANSLATION_METHOD_SITE_GROUP
        ];
    }

    /**
     * @return mixed[]
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = ['includeDirectory', 'trim'];
        $rules[] = ['includeDirectory', 'required'];
        return $rules;
    }

    public function getSettingsHtml(): ?string
    {

        return Cp::textFieldHtml([
            'label' => 'Directory',
            'instructions' => 'Path to the directory (relative to the templates folder) where the included files live. Use %SITE% / %SITEGROUP% for site (group) specific templates. %SITEGROUP% will look in the first site of this group',
            'id' => 'includeDirectory',
            'name' => 'includeDirectory',
            'value' => $this->includeDirectory,
            'errors' => $this->getErrors('includeDirectory'),
        ]);
    }

    /**
     * @inheritDoc
     */
    // Returns the HTML that should be shown in the field input
    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
    {
        /** @var Entry $entry */
        $entry = $element;

        // Start with an empty option
        $options = [
            ['label' => '---', 'value' => '']
        ];

        try {
            // Get the base directory and read its files
            $baseDir = $this->getBaseDirectory($entry, $this->includeDirectory);

            $files = scandir($baseDir);
            $files = array_diff($files, ['..', '.']);

            // Add each file to the options
            foreach ($files as $file) {
                if (!str_starts_with($file, '_') && !is_dir($baseDir . DIRECTORY_SEPARATOR . $file)) {
                    $label = str_replace(['-','_','.twig'], [' ', ' ',''], $file);
                    $options[] = ['label' => Craft::t('site', ucwords($label)), 'value' => $file];
                }
            }
        } catch (Exception) {
            if ($value) {
                $options[] = ['label' => $value, 'value' => $value];
            }
        }

        // Return a selectize field
        return Cp::selectizeHtml([
            'name' => $this->handle,
            'value' => $value,
            'options' => $options,
            'selectizeOptions' => [
                'allowEmptyOption' => true,
            ],
        ]);
    }

    // Helper method to generate the base directory path
    protected function getBaseDirectory($entry, $includeDirectory): string
    {
        // Replace %SITE% with the current site's handle
        if (str_contains($includeDirectory, '%SITE%')) {
            $includeDirectory = str_replace('%SITE%', $entry->site->handle, $includeDirectory);
        }

        // Replace %SITEGROUP% with the first site's handle in the current site group
        if (str_contains($includeDirectory, '%SITEGROUP%')) {
            $siteHandle = $entry->site->getGroup()->getSites()[0]->handle;
            $includeDirectory = str_replace('%SITEGROUP%', $siteHandle, $includeDirectory);
        }

        // Return the full directory path
        return App::parseEnv('@templates') . DIRECTORY_SEPARATOR . $includeDirectory;
    }


}
