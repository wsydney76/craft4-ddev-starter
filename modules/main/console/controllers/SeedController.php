<?php

namespace modules\main\console\controllers;

use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\ArrayHelper;
use craft\helpers\Console;
use craft\helpers\FileHelper;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Collection;
use yii\console\ExitCode;
use function is_dir;
use function ucwords;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class SeedController extends BaseController

{
    public const NUM_ENTRIES = 50;
    public const SECTION_HANDLE = 'news';
    public string $volume = 'images';
    public int $minWidth = 1200;

    /**
     * Create a number of fake entries
     *
     * @param int $num Max number of entries that will be created.
     * Will be set to the number of images in the specified folder if lower.
     * @param string $sectionHandle Section handle for the created entries
     * @param string $folderName Name of the folder where images live
     * @return int
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionCreateEntries(int $num = self::NUM_ENTRIES, string $sectionHandle = self::SECTION_HANDLE, string $folderName = 'starter'): int
    {
        $section = Craft::$app->sections->getSectionByHandle($sectionHandle);
        if (!$section) {
            $this->stderr("Invalid section {$sectionHandle}") . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->indexImages();

        Craft::$app->runAction('main/seed/img-add-provisional-texts', [$folderName]);

        $folder = Craft::$app->assets->findFolder(['name' => $folderName]);
        if (!$folder) {
            Console::output('Folder not found');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $query = Asset::find()
            ->kind('image')
            ->volume($this->volume)
            ->folderId($folder->id)
            ->width('> ' . $this->minWidth)
            ->orderBy(Craft::$app->db->driverName === 'mysql' ? 'RAND()' : 'RANDOM()');


        $images = $query->collect();

        $num = min($num, $images->count());

        if ($num < 12) {
            $this->stdout('Could not find enough images' . PHP_EOL);
            return ExitCode::OK;
        }

        if ($this->interactive && !$this->confirm("Create {$num} entries of type '{$section->name}'? Make sure a number of images exist!", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Creating {$num} entries of type '{$section->name}'." . PHP_EOL);

        $faker = Factory::create();

        $type = $section->getEntryTypes()[0];

        for ($i = 1; $i <= $num; ++$i) {

            $title = $faker->text(50);
            $this->stdout("[{$i}/{$num}] Creating ... ");

            $image = $this->getRandomImage($this->minWidth);

            $entry = $this->createEntry([
                'section' => $section->handle,
                'type' => $type->handle,
                'author' => User::find()->orderBy('rand()')->one(),
                'title' => $title,
                'postDate' => $faker->dateTimeInInterval('-14 days', '-3 months'),
                'fields' => [
                    'tagline' => $faker->text(50),
                    'featuredImage' => [$images[$i - 1]->id],
                    'bodyContent' => $this->getBodyContent($faker)
                ]

            ]);

            $this->translateHint($entry, 'de');
        }

        return ExitCode::OK;
    }

    /**
     * Create a fake hero area entry and attach it to the home page.
     *
     * @return int
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function actionCreateHeroArea(): int
    {

        if ($this->interactive && !$this->confirm("Create hero area for homepage?", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        // home, sweet home
        $image = Asset::find()->filename('ammersee-2.jpg')->one();

        if (!$image) {
            // homeless
            $image = $this->getRandomImage($this->minWidth);
        }

        // Starter images not yet indexed?
        if (!$image) {
            $this->indexImages();
            $image = $this->getRandomImage($this->minWidth);
        }

        if ($image) {
            $faker = Factory::create();
            $paragraphs = '';
            foreach ($faker->paragraphs(2) as $paragraph) {
                $paragraphs .= $paragraph . PHP_EOL . PHP_EOL;
            }

            // pseudo random
            $target = Entry::find()->section('news')->orderBy('title')->one();
            $target2 = Entry::find()->section('news')->offset(1)->orderBy('title')->one();

            $heroAreaEntry = $this->createEntry([
                'section' => 'heroArea',
                'type' => 'default',
                'title' => 'Craft Starter',
                'slug' => 'craft-starter',
                'fields' => [
                    'heroText' => $paragraphs,
                    'heroImage' => [$image->id],
                    'heroAreaTemplate' => 'textimagehalfed.twig',
                    'ctaButtons' => [
                        [
                            'type' => 'button',
                            'fields' => [
                                'ctaTarget' => $target ? [$target->id] : [],
                                'ctaCaption' => $faker->text(20),
                                'primary' => true,
                            ]
                        ],
                        [
                            'type' => 'button',
                            'fields' => [
                                'ctaTarget' => $target2 ? [$target2->id] : [],
                                'ctaCaption' => $faker->text(20),
                                'primary' => false,
                            ]
                        ]
                    ]
                ]
            ]);

            if ($heroAreaEntry) {
                $homepageEntry = Entry::find()->section('page')->type('home')->one();
                if ($homepageEntry) {
                    $homepageEntry->setFieldValue('heroArea', [$heroAreaEntry->id]);
                    Craft::$app->elements->saveElement($homepageEntry);
                }
            }
        }

        return ExitCode::OK;
    }

    protected function translateHint(Entry $entry, string $siteHandle): void
    {
        $entry = $entry->getLocalized()->site($siteHandle)->one();
        if (!$entry) {
            return;
        }

        /** @var Collection $primaryBlocks */
        $primaryBlocks = $entry->getFieldValue('bodyContent')->collect();

        $entry->setFieldValue('bodyContent', [
            // Not sure whether ->ids() respects the correct order, so play it safe...
            'sortOrder' => $primaryBlocks->map(function($entry) {
                return $entry->id;
            }),
            'blocks' => [
                $primaryBlocks->last()->id => [
                    'type' => 'text',
                    'fields' => [
                        'text' => 'Dies ist ein automatisch erstellter Beispieleintrag.'
                    ]
                ]
            ]
        ]);

        Craft::$app->elements->saveElement($entry);
    }

    protected function getRandomImage($width = 1900)
    {
        return Asset::find()
            ->volume($this->volume)
            ->kind('image')
            ->width('> ' . $width)
            ->orderBy(Craft::$app->db->driverName === 'mysql' ? 'RAND()' : 'RANDOM()')
            ->one();
    }

    /**
     * @return array<string, array<array<string, string|array<string, string>|array<string, mixed[]|string|null>|array<string, mixed[]>>|string>>
     */
    protected function getBodyContent(Generator $faker): array
    {

        $content = [
            'sortOrder' => [],
            'blocks' => []
        ];

        $folder = Craft::$app->assets->findFolder(['name' => 'starter']);

        $layouts = [
            ['text', 'heading', 'image', 'text', 'image'],
            ['text', 'heading', 'image', 'text', 'quote', 'text'],
            ['text', 'text', 'text', 'heading', 'text', 'text', 'text', 'heading', 'text', 'text', 'text'],
            ['text', 'image', 'image', 'image'],
            ['text', 'heading', 'gallery', 'image', 'image'],
        ];

        $blockTypes = $faker->randomElement($layouts);

        $i = 0;
        foreach ($blockTypes as $blockType) {

            switch ($blockType) {
                case 'text':
                    $paragraphs = '';
                    foreach ($faker->paragraphs($faker->numberBetween(1, 5)) as $paragraph) {
                        $paragraphs .= $paragraph . PHP_EOL . PHP_EOL;
                    }

                    $block = [
                        'type' => 'text',
                        'fields' => [
                            'text' => $paragraphs
                        ]
                    ];
                    break;
                case 'heading':
                    $block = [
                        'type' => 'heading',
                        'fields' => [
                            'text' => $faker->text(40),
                            'htmlTag' => 'h2'
                        ]
                    ];
                    break;
                case 'quote':
                    $block = [
                        'type' => 'quote',
                        'fields' => [
                            'text' => $faker->text(80),
                            'cite' => $faker->name
                        ]
                    ];
                    break;
                case 'image':
                    $image = $this->getRandomImage(900);
                    $block = [
                        'type' => 'image',
                        'fields' => [
                            'image' => $image ? [$image->id] : null,
                            'caption' => $faker->text(30),
                            'align' => 'wide',
                            'aspectRatio' => 'default'
                        ]
                    ];
                    break;
                case 'gallery':
                    $ids = Asset::find()
                        ->kind('image')
                        ->volume($this->volume)
                        ->folderId($folder->id)
                        ->width('> 900')
                        ->limit(6)
                        ->orderBy(Craft::$app->db->driverName === 'mysql' ? 'RAND()' : 'RANDOM()')
                        ->ids();

                    $block = [
                        'type' => 'gallery',
                        'fields' => [
                            'images' => $ids,
                        ]
                    ];
                    break;
            }

            ++$i;
            $id = "new{$i}";
            $content['sortOrder'][] = $id;
            $content['blocks'][$id] = $block;
        }

        $id = 'newExample';
        $content['sortOrder'][] = $id;
        $content['blocks'][$id] = [
            'type' => 'text',
            'fields' => [
                'text' => 'This is an automatically generated sample entry.'
            ]
        ];

        return $content;
    }

    /**
     * Downloads and imports a number of images from Unsplash
     *
     * @param int $num Number of images to be created
     * @param int $timeout The Timeout in seconds for each call to Unsplash
     * @param string $folderName The name of the folder where the images will be stored
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     * @throws \craft\errors\AssetDisallowedExtensionException
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\MissingAssetException
     * @throws \craft\errors\VolumeException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */

    public function actionCreateImages(int $num = 30, int $timeout = 10, string $folderName = 'examples'): int
    {

        if ($this->interactive && !$this->confirm("Download {$num} example images from Unsplash?")) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Trying to download {$num} example images from Unsplash (Timeout {$timeout} sec.)" . PHP_EOL);

        $client = Craft::createGuzzleClient();

        $volume = Craft::$app->volumes->getVolumeByHandle('images');
        if (!$volume) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $path = App::parseEnv($volume->fs->path) . DIRECTORY_SEPARATOR . $folderName;
        if (!is_dir($path)) {
            FileHelper::createDirectory($path);
        }

        // Don't overwrite existing images, ensure sequence number is unique
        $folders = Craft::$app->assets->findFolders(['volumeId' => $volume->id, 'path' => $folderName . '/']);
        if ($folders) {
            $count = Asset::find()->folderId(ArrayHelper::firstValue($folders)->id)->count();
        } else {
            $count = 0;
        }

        $start = $count + 1;
        $end = $count + $num;

        $loop = 0;

        $session = Craft::$app->assetIndexer->createIndexingSession([$volume]);

        for ($i = $start; $i <= $end; ++$i) {

            ++$loop;

            $filename = "example_{$i}.jpg";

            $this->stdout("[{$loop}/{$num}] " . $filename . "...");

            $url = "https://picsum.photos/2000/1280";

            try {
                $client->get($url, ['sink' => $path . DIRECTORY_SEPARATOR . $filename, 'timeout' => $timeout]);
            } catch (Exception $exception) {
                $this->stdout(" failed: {$exception->getMessage()} \n");
                continue;
            }

            $asset = Craft::$app->assetIndexer->indexFile($volume, 'examples/' . $filename, $session->id);

            $asset->setFieldValue('copyright', 'Unsplash via picsum.photos');
            $asset->setFieldValue('altText', 'Platzhaltertext');
            Craft::$app->elements->saveElement($asset);

            $localizedAsset = $asset->getLocalized()->site('en')->one();
            if ($localizedAsset) {
                $localizedAsset->setFieldValue('altText', 'Placeholder text');
                Craft::$app->elements->saveElement($localizedAsset);
            }

            $this->stdout(" created\n");
        }

        return ExitCode::OK;
    }

    /**
     * Make sure images in the specified folder have alt text and copyright.
     * This will make sure that entries relating to these images can be saved without errors.
     *
     * @param string $folderName The folder name where to check the images
     * @return int
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function actionImgAddProvisionalTexts(string $folderName = 'starter'): int
    {
        $folder = Craft::$app->assets->findFolder(['name' => $folderName]);
        if (!$folder) {
            return ExitCode::OK;
        }

        $hasNoAlt = Asset::find()
            ->site('*')
            ->folderId($folder->id)
            ->kind('image')
            ->altText(':empty:')
            ->exists();

        if (!$hasNoAlt) {
            return ExitCode::OK;
        }

        if ($this->interactive && $this->confirm("Add provisional alt text/copyright to images?", true)) {
            foreach (Craft::$app->sites->allSites as $site) {
                $images = Asset::find()
                    ->kind('image')
                    ->volume($this->volume)
                    ->site($site->handle)
                    ->all();

                foreach ($images as $image) {
                    $save = false;
                    if (!$image->altText) {
                        $image->altText = ucwords($image->title);
                        $save = true;
                    }
                    if (!$image->copyright) {
                        $image->copyright = 'tbd.';
                        $save = true;
                    }
                    if ($save) {
                        $this->stdout("Saving provisional alt text / copyright to $image->title ($site->name)" . PHP_EOL);
                        Craft::$app->elements->saveElement($image, false, true, false);
                    }
                }
            }
        }
        return ExitCode::OK;
    }

    protected function indexImages(): void
    {
        $imagesCount = Asset::find()
            ->volume($this->volume)
            ->kind('image')
            ->width("> $this->minWidth")
            ->count();

        if ($imagesCount < 20) {
            $this->stdout("Indexing existing images..." . PHP_EOL);
            Craft::$app->runAction('index-assets/one', [$this->volume]);
        }
    }
}