<?php

namespace modules\main\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\Entry;
use craft\helpers\App;
use craft\helpers\Cp;
use Exception;
use function is_dir;
use function str_contains;
use function str_replace;
use function ucwords;
use const CRAFT_TEMPLATES_PATH;
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
    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
    {

        /** @var Entry $entry */
        $entry = $element;
        $options = [
            ['label' => '---', 'value' => '']
        ];

        try {

            $baseDir = $this->getBaseDirectory($entry, $this->includeDirectory);

            $files = scandir($baseDir);
            $files = array_diff($files, ['..', '.']);

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

        return Cp::selectHtml([
            'name' => $this->handle,
            'value' => $value,
            'options' => $options
        ]);
    }

    protected function getBaseDirectory($entry, $includeDirectory): string
    {

        if (str_contains($includeDirectory, '%SITE%')) {
            $includeDirectory = str_replace('%SITE%', $entry->site->handle, $includeDirectory);
        }

        if (str_contains($includeDirectory, '%SITEGROUP%')) {
            // get first site in sitegroup, where the templates live
            $siteHandle = $entry->site->getGroup()->getSites()[0]->handle;
            $includeDirectory = str_replace('%SITEGROUP%', $siteHandle, $includeDirectory);
        }

        return App::parseEnv('@templates') . DIRECTORY_SEPARATOR . $includeDirectory;
    }

}
