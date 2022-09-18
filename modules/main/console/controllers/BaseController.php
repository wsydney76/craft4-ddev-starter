<?php

namespace modules\main\console\controllers;

use Craft;
use craft\console\Controller;
use craft\elements\Entry;
use craft\elements\User;
use craft\fields\Matrix;
use craft\helpers\ArrayHelper;
use function implode;

class BaseController extends Controller
{
    protected function createEntry(array $data, bool $overwrite = false): mixed
    {

        $user = User::find()->admin()->one();

        ['section' => $sectionHandle, 'type' => $typeHandle, 'title' => $title] = $data;

        if (isset($data['slug']) && $overwrite === false) {
            $entry = Entry::find()->section($sectionHandle)->slug($data['slug'])->one();

            if ($entry) {
                $this->stdout("Entry $title exists" . PHP_EOL);
                return $entry;
            }
        }

        $section = Craft::$app->sections->getSectionByHandle($sectionHandle);

        if (!$section) {
            $this->stdout("Section $sectionHandle does not exist." . PHP_EOL);
            return null;
        }

        $type = ArrayHelper::firstWhere($section->getEntryTypes(), 'handle', $typeHandle);
        if (!$type) {
            $this->stdout("Type $typeHandle does not exist." . PHP_EOL);
            return null;
        }

        $entry = new Entry([
            'sectionId' => $section->id,
            'typeId' => $type->id,
            'title' => $title
        ]);

        if (isset($data['slug'])) {
            $entry->slug = $data['slug'];
        }

        if (isset($data['author'])) {
            $entry->authorId = $data['author']->id;
        } else {
            $entry->authorId = $user->id;
        }

        if (isset($data['postDate'])) {
            $entry->postDate = $data['postDate'];
        }

        if (isset($data['parent'])) {
            $entry->setParentId($data['parent']->id);
        }

        if (isset($data['fields'])) {
            foreach ($data['fields'] as $handle => $value) {
                $field = Craft::$app->fields->getFieldByHandle($handle);

                if ($field instanceof Matrix) {
                    $blocks = $value;
                    $value = ['sortOrder' => [], 'blocks' => []];
                    foreach ($blocks as $i => $block) {
                        $value['sortOrder'][] = $i;
                        $value['blocks'][$i] = $block;
                    }
                }

                $entry->setFieldValue($handle, $value);
            }
        }

        if (!Craft::$app->elements->saveElement($entry)) {
            $this->stdout("Error saving entry $title: " . implode(', ', $entry->getErrorSummary(true)) . PHP_EOL);
            return null;
        }

        $this->stdout("Entry $title saved." . PHP_EOL);

        if (isset($data['localized'])) {
            foreach ($data['localized'] as $siteHandle => $localizedContent) {
                $localizedEntry = $entry->getLocalized()->site($siteHandle)->one();
                if ($localizedEntry) {
                    if (isset($localizedContent['title'])) {
                        $localizedEntry->title = $localizedContent['title'];
                    }
                    if (isset($localizedContent['slug'])) {
                        $localizedEntry->slug = $localizedContent['slug'];
                    }

                    if (isset($localizedContent['fields'])) {
                        foreach ($localizedContent['fields'] as $handle => $value) {

                            $field = Craft::$app->fields->getFieldByHandle($handle);
                            if ($field instanceof Matrix) {
                                $blocks = $value;

                                $ids = $entry->getFieldValue($handle)->ids();

                                $value = ['sortOrder' => $ids, 'blocks' => []];
                                foreach ($blocks as $i => $block) {
                                    $value['blocks'][$ids[$i]] = $block;
                                }
                            }

                            $localizedEntry->setFieldValue($handle, $value);
                        }
                    }

                    if (!Craft::$app->elements->saveElement($localizedEntry)) {
                        $this->stdout("Error saving localized entry $siteHandle $title: " . implode(', ', $localizedEntry->getErrorSummary(true)) . PHP_EOL);
                    }
                }
            }
        }

        return $entry;
    }
}