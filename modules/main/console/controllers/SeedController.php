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
use craft\helpers\StringHelper;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Collection;
use yii\console\ExitCode;
use function is_dir;
use function str_replace;
use function ucwords;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class SeedController extends InitController

{
    public const NUM_ENTRIES = 50;
    public const SECTION_HANDLE = 'article';

    protected Generator $faker;

    public function beforeAction($action): bool
    {
        $this->faker = Factory::create();
        return parent::beforeAction($action);
    }


    public function actionCreateTopics(): int
    {
        if ($this->interactive && !$this->confirm("Create topic entries?", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        Console::output('Creating topic entries.');

        $topics = [
            [
                'en' => 'Sport',
                'de' => 'Sport',
                'children' => [
                    [
                        'en' => 'Football',
                        'de' => 'Fußball',
                        'children' => [
                            ['en' => 'Bundesliga', 'de' => 'Bundesliga'],
                            ['en' => 'Premier League', 'de' => 'Premier League'],
                            ['en' => 'International', 'de' => 'International'],
                        ]
                    ],
                    [
                        'en' => 'Tennis',
                        'de' => 'Tennis',
                        'children' => [
                            ['en' => 'Majors', 'de' => 'Majors']
                        ]
                    ],
                    [
                        'en' => 'Golf',
                        'de' => 'Golf'
                    ]

                ]
            ],
            [
                'en' => 'Entertainment',
                'de' => 'Unterhaltung',
                'children' => [
                    ['en' => 'Music', 'de' => 'Musik'],
                    ['en' => 'Cinema', 'de' => 'Kino'],
                ]
            ],
            [
                'en' => 'Opinion',
                'de' => 'Meinung'
            ]
        ];

        foreach ($topics as $topic) {
            $this->createTopic($topic);
        }


        return ExitCode::OK;
    }

    protected function createTopic($topic, ?Entry $parent = null): void
    {
        $entry = $this->createEntry([
            'section' => 'topic',
            'type' => 'default',
            'site' => 'en',
            'parent' => $parent,
            'title' => $topic['en'],
            'slug' => StringHelper::slugify($topic['en']),
            'fields' => [
                'tagline' => $this->faker->text(30),
                'featuredImage' => [$this->getRandomImage()->id ?? null]
            ],
            'localized' => [
                'de' => [
                    'title' => $topic['de'],
                    'slug' => StringHelper::slugify($topic['de'])
                ]
            ]
        ]);

        if (isset($topic['children'])) {
            foreach ($topic['children'] as $child) {
                $this->createTopic($child, $entry);
            }
        }
    }

    /**
     * Create a number of fake entries
     *
     * @param int $num Max number of entries that will be created.
     * Will be set to the number of images in the specified folder if lower.
     * @param string $sectionHandle Section handle for the created entries
     * @param string $path Path of the folder where images live. Ends with '/'
     * @return int
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionCreateArticles(int $num = self::NUM_ENTRIES, string $sectionHandle = self::SECTION_HANDLE, string $path = 'starter/'): int
    {
        $section = Craft::$app->sections->getSectionByHandle($sectionHandle);
        if (!$section) {
            $this->stderr("Invalid section {$sectionHandle}") . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->indexImages();

        $this->actionImgAddProvisionalTexts($path);

        $images = $this->getImagesFromFolder($path);

        $num = min($num, $images->count());

        if (!$num) {
            $this->stdout('Could not find enough images' . PHP_EOL);
            return ExitCode::OK;
        }

        if ($this->interactive && !$this->confirm("Create {$num} entries of type '{$section->name}'? Make sure a number of images exist!", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Creating {$num} entries of type '{$section->name}'." . PHP_EOL);

        $type = $section->getEntryTypes()[0];

        for ($i = 1; $i <= $num; ++$i) {

            $title = $this->faker->text(50);
            $this->stdout("[{$i}/{$num}] ");

            $entry = $this->createEntry([
                'section' => $section->handle,
                'type' => $type->handle,
                'author' => User::find()->orderBy('rand()')->one(),
                'title' => str_replace('.', '', $title),
                'postDate' => $this->faker->dateTimeInInterval('-2 days', '-3 months'),
                'fields' => [
                    'isFeatured' => $this->faker->boolean(25),
                    'tagline' => $this->faker->text(50),
                    'teaser' => $this->faker->text(15),
                    'featuredImage' => [$images[$i - 1]->id],
                    'bodyContent' => $this->getBodyContent(),
                    'topics' => [$this->getRandomTopic()->id ?? null]
                ]

            ]);

            $this->translateHint($entry, 'de');
        }

        // Add a video example

        $entry = $this->createEntry([
            'section' => 'article',
            'type' => 'default',
            'author' => User::find()->orderBy('rand()')->one(),
            'title' => 'Video Demo',
            'fields' => [
                'isFeatured' => true,
                'tagline' => 'Example for Video and GDPR',
                'teaser' => $this->faker->text(15),
                'featuredImage' => [$this->getRandomImage(1200)->id],
                'bodyContent' => [
                    [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->faker->text(250)
                        ]
                    ],
                    [
                        'type' => 'youtubeVideo',
                        'fields' => [
                            'heading' => $this->faker->text(30),
                            'text' => $this->faker->text(100),
                            'key' => 'uekZpkYf7-E'
                        ]
                    ],
                    [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->faker->text(250)
                        ]
                    ],
                    [
                        'type' => 'youtubeVideo',
                        'fields' => [
                            'heading' => $this->faker->text(30),
                            'text' => $this->faker->text(100),
                            'key' => 'vrjad-k-eeo'
                        ]
                    ]
                ]
            ],
            'localized' => [
                'de' => [
                    'fields' => [
                        'tagline' => 'Beispiel für Video und DSGVO',
                    ]
                ]
            ]

        ]);

        return ExitCode::OK;
    }

    public function actionCreateStories(int $num = 3, string $path = 'starter/'): int
    {
        $section = Craft::$app->sections->getSectionByHandle('article');
        if (!$section) {
            $this->stderr("Invalid section article") . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        // $this->indexImages();

        // $this->actionImgAddProvisionalTexts($path);

        $images = $this->getImagesFromFolder($path);

        $num = min($num, $images->count());

        if (!$num) {
            $this->stdout('Could not find enough images' . PHP_EOL);
            return ExitCode::OK;
        }

        if ($this->interactive && !$this->confirm("Create {$num} entries of type Article/Story? Make sure a number of images exist!", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Creating {$num} entries of type 'Story'." . PHP_EOL);

        $type = ArrayHelper::firstWhere($section->entryTypes, 'handle', 'story');


        for ($i = 1; $i <= $num; ++$i) {

            $title = $this->faker->text(30);
            $this->stdout("[{$i}/{$num}] ");

            $entry = $this->createEntry([
                'section' => $section->handle,
                'type' => $type->handle,
                'author' => User::find()->orderBy('rand()')->one(),
                'title' => str_replace('.', '', $title),
                'postDate' => $this->faker->dateTimeInInterval('-1 days', '-10 days'),
                'fields' => [
                    'isFeatured' => $this->faker->boolean(25),
                    'tagline' => $this->faker->text(50),
                    'teaser' => $this->faker->text(15),
                    'featuredImage' => [$images[$i - 1]->id],
                    'storyContent' => $this->getStoryContent($i),
                    'topics' => [$this->getRandomTopic()->id ?? null]
                ]

            ]);

            $this->translateHint($entry, 'de', 'storyContent');
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
    public function actionCreateHomepageContent(): int
    {

        if ($this->interactive && !$this->confirm("Create some content for homepage?", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        Console::output('Creating homepage content.');

        $homepageEntry = Entry::find()->section('page')->type('home')->one();

        if (!$homepageEntry) {
            Console::error('No homepage entry found');
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

            // pseudo random
            $target = Entry::find()->section('article')->orderBy('title')->one();
            $target2 = Entry::find()->section('article')->offset(1)->orderBy('title')->one();

            $heroAreaEntry = $this->createEntry([
                'section' => 'heroArea',
                'type' => 'default',
                'site' => 'en',
                'title' => 'Craft Starter',
                'slug' => 'hero1',
                'fields' => [
                    'body' => $this->getMarkdownParagraphs(2),
                    'image' => [$image->id],
                    'heroAreaTemplate' => 'split.twig',
                    'buttons' => [
                        [
                            'type' => 'button',
                            'fields' => [
                                'target' => $target ? [$target->id] : [],
                                'caption' => $this->faker->text(20),
                                'primary' => true,
                            ]
                        ],
                        [
                            'type' => 'button',
                            'fields' => [
                                'target' => $target2 ? [$target2->id] : [],
                                'caption' => $this->faker->text(20),
                                'primary' => false,
                            ]
                        ]
                    ]
                ]
            ]);

            $this->actionCreatePersons();

            if ($heroAreaEntry) {
                $homepageEntry->setFieldValue('heroArea', [$heroAreaEntry->id]);
            }

            $homepageEntry->setFieldValue('bodyContent', [
                'sortOrder' => ['new1', 'new2'],
                'blocks' => [
                    'new1' => [
                        'type' => 'heading',
                        'fields' => [
                            'text' => $this->faker->text(50),
                            'htmlTag' => 'h2'
                        ]
                    ],
                    'new2' => [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->getMarkdownParagraphs(3),

                        ]
                    ]
                ]

            ]);

            $contentComponents = [];


            $persons = Entry::find()->section('person')->collect();
            $icons = $this->getImagesFromFolder('icons/starter/', 20, 4);

            $contentComponents[] = $this->createEntry([
                'section' => 'testimonial',
                'type' => 'testimonial',
                'site' => 'en',
                'title' => '',
                'slug' => 'testimonial1',
                'fields' => [
                    'body' => $this->faker->text(250),
                    'person' => [$persons[0]->id],
                    'align' => 'right',
                    'testimonialStyle' => 'overlappingImage',
                ]
            ]);


            $contentComponents[] = $this->createEntry([
                'section' => 'features',
                'type' => 'features',
                'site' => 'en',
                'title' => $this->faker->text(60),
                'slug' => 'features1',
                'fields' => [
                    'teaser' => $this->faker->text(30),
                    'body' => $this->faker->text(200),
                    'features' => [
                        [
                            'type' => 'feature',
                            'fields' => [
                                'icon' => [$icons[0]->id],
                                'heading' => $this->faker->text(30),
                                'text' => $this->faker->text(120)
                            ]
                        ],
                        [
                            'type' => 'feature',
                            'fields' => [
                                'icon' => [$icons[1]->id],
                                'heading' => $this->faker->text(30),
                                'text' => $this->faker->text(120)
                            ]
                        ],
                        [
                            'type' => 'feature',
                            'fields' => [
                                'icon' => [$icons[2]->id],
                                'heading' => $this->faker->text(30),
                                'text' => $this->faker->text(120)
                            ]
                        ],
                        [
                            'type' => 'feature',
                            'fields' => [
                                'icon' => [$icons[3]->id],
                                'heading' => $this->faker->text(30),
                                'text' => $this->faker->text(120)
                            ]
                        ],
                    ]
                ]
            ]);


            $contentComponents[] = $this->createEntry([
                'section' => 'testimonial',
                'type' => 'testimonial',
                'site' => 'en',
                'title' => '',
                'slug' => 'testimonial2',
                'fields' => [
                    'body' => $this->faker->text(250),
                    'person' => [$persons[1]->id],
                    'align' => '',
                    'testimonialStyle' => 'overlappingImage',
                ]
            ]);

            $contentComponents[] = $this->createEntry([
                'section' => 'team',
                'type' => 'team',
                'site' => 'en',
                'title' => 'Our Team',
                'slug' => 'team',
                'fields' => [
                    'persons' => Entry::find()->section('person')->limit(4)->ids(),
                    'body' => $this->faker->text(200)
                ],
                'localized' => [
                    'de' => [
                        'title' => 'Unser Team'
                    ]
                ]
            ]);


            $image = Asset::find()->filename('blind.jpg')->one();
            $contentComponents[] = $this->createEntry([
                'section' => 'heroArea',
                'type' => 'default',
                'site' => 'en',
                'title' => $this->faker->text(30),
                'slug' => 'hero2',
                'fields' => [
                    'body' => $this->faker->text(200),
                    'image' => [$image->id],
                    'heroAreaTemplate' => 'minor.twig',
                    'buttons' => [
                        [
                            'type' => 'button',
                            'fields' => [
                                'target' => $target ? [$target->id] : [],
                                'caption' => $this->faker->text(20),
                                'primary' => true,
                            ]
                        ],
                        [
                            'type' => 'button',
                            'fields' => [
                                'target' => $target2 ? [$target2->id] : [],
                                'caption' => $this->faker->text(20),
                                'primary' => false,
                            ]
                        ]
                    ]
                ]
            ]);


            $articleIndex = Entry::find()
                ->section('page')
                ->type('articleIndex')
                ->one();

            $contentComponents[] = $this->createEntry([
                'section' => 'cards',
                'type' => 'cards',
                'site' => 'en',
                'title' => 'Latest Articles',
                'slug' => 'latest-articles',
                'fields' => [
                    'criteria' => ['language' => 'json', 'value' => '{"section": "article", "type": "default", "limit": 9, "showMetaData": true}'],
                    'buttons' => [
                        [
                            'type' => 'button',
                            'fields' => [
                                'target' => $articleIndex ? [$articleIndex->id] : [],
                                'caption' => 'Show all Articles',
                                'primary' => true,
                            ]
                        ]
                    ]
                ],
                'localized' => [
                    'de' => [
                        'title' => 'Aktuelle Artikel',
                        'fields' => [
                            'buttons' => [
                                [
                                    'type' => 'button',
                                    'fields' => [
                                        'target' => $articleIndex ? [$articleIndex->id] : [],
                                        'caption' => 'Alle Artikel anzeigen',
                                        'primary' => true,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $contentComponents[] = $this->createEntry([
                'section' => 'cards',
                'type' => 'cards',
                'site' => 'en',
                'title' => 'Latest Stories',
                'slug' => 'latest-stories',
                'fields' => [
                    'criteria' => ['language' => 'json', 'value' => '{"section": "article", "type": "story", "limit": 3}'],
                ],
                'localized' => [
                    'de' => [
                        'title' => 'Neueste Stories',
                        'slug' => 'neueste-stories',
                    ]
                ]
            ]);

            $topicsIndex = Entry::find()
                ->section('page')
                ->type('topicIndex')
                ->one();

            $contentComponents[] = $this->createEntry([
                'section' => 'cards',
                'type' => 'cards',
                'site' => 'en',
                'title' => 'Topics',
                'slug' => 'topics',
                'fields' => [
                    'criteria' => ['language' => 'json', 'value' => '{"section": "topic", "level": 1}'],
                    'buttons' => [
                        [
                            'type' => 'button',
                            'fields' => [
                                'target' => $topicsIndex ? [$topicsIndex->id] : [],
                                'caption' => 'Show all Topics',
                                'primary' => true,
                            ]
                        ]
                    ]
                ],
                'localized' => [
                    'de' => [
                        'title' => 'Themen',
                        'slug' => 'themen',
                        'fields' => [
                            'buttons' => [
                                [
                                    'type' => 'button',
                                    'fields' => [
                                        'target' => $topicsIndex ? [$topicsIndex->id] : [],
                                        'caption' => 'Alle Themen anzeigen',
                                        'primary' => true,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $contentComponents[] = $this->createEntry([
                'section' => 'faqs',
                'type' => 'faqs',
                'site' => 'en',
                'title' => 'FAQs',
                'slug' => 'faqs',
                'fields' => [
                    'faqs' => [
                        [
                            'type' => 'faq',
                            'fields' => [
                                'question' => str_replace('.', '?', $this->faker->text(40)),
                                'answer' => $this->faker->text(300)
                            ]
                        ],
                        [
                            'type' => 'faq',
                            'fields' => [
                                'question' => str_replace('.', '?', $this->faker->text(40)),
                                'answer' => $this->faker->text(300)
                            ]
                        ],
                        [
                            'type' => 'faq',
                            'fields' => [
                                'question' => str_replace('.', '?', $this->faker->text(40)),
                                'answer' => $this->faker->text(300)
                            ]
                        ],
                    ]
                ]
            ]);


            if ($contentComponents) {
                $contentComponents = collect($contentComponents);
                if ($homepageEntry) {
                    $homepageEntry->setFieldValue('contentComponents', $contentComponents->map(fn($e) => $e->id)->toArray());
                }
            }

            Craft::$app->elements->saveElement($homepageEntry);
        }

        return ExitCode::OK;
    }

    public function actionCreatePersons(): int
    {
        $names = ['Erna Klawuppke', 'Aisha Conelly', 'Miram Zboncak', 'Aylin Müller-Lüdenscheidt'];
        $teasers = ['Project Manager', 'Frontend Developer', 'Junior Designer', 'Head of Development'];


        $this->actionImgAddProvisionalTexts('photos/starter/', 'Tailwind UI');

        $images = $this->getImagesFromFolder('photos/starter/', 800, 4);

        foreach ($images as $i => $image) {
            $name = $names[$i];
            $slug = StringHelper::slugify($name);
            $person = $this->createEntry([
                'section' => 'person',
                'type' => 'default',
                'site' => 'en',
                'title' => $name,
                'slug' => $slug,
                'fields' => [
                    'photo' => [$image->id],
                    'body' => $this->faker->text(200),
                    'teaser' => $teasers[$i],
                    'socialLinks' => [
                        ['col1' => 'mastodon', 'col2' => 'https://joinmastodon.org'],
                        ['col1' => 'email', 'col2' => 'email:name@example.com'],
                    ]
                ]
            ]);
        }
        return ExitCode::OK;
    }

    protected function translateHint(Entry $entry, string $siteHandle, string $fieldHandle = 'bodyContent'): void
    {
        $entry = $entry->getLocalized()->site($siteHandle)->one();
        if (!$entry) {
            return;
        }

        /** @var Collection $primaryBlocks */
        $primaryBlocks = $entry->getFieldValue($fieldHandle)->collect();

        $entry->setFieldValue($fieldHandle, [
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

    protected function getRandomImage($width = 1900): ?Asset
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
    protected function getBodyContent(): array
    {

        $localFaker = Factory::create('de_DE');

        $content = [
            'sortOrder' => [],
            'blocks' => []
        ];

        $folder = Craft::$app->assets->findFolder(['path' => 'starter/']);

        $layouts = [
            ['text', 'heading', 'image', 'text', 'image'],
            ['text', 'heading', 'image', 'text', 'quote', 'text'],
            ['longtext', 'heading', 'longtext', 'heading', 'longtext'],
            ['text', 'image', 'image', 'image'],
            ['text', 'heading', 'gallery', 'text', 'heading', 'text'],
        ];

        $blockTypes = $this->faker->randomElement($layouts);

        $i = 0;
        foreach ($blockTypes as $blockType) {

            switch ($blockType) {
                case 'text':
                    $block = [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->getMarkdownParagraphs($this->faker->numberBetween(1, 5))
                        ]
                    ];
                    break;
                case 'longtext':
                    $block = [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->getMarkdownParagraphs($this->faker->numberBetween(6, 12))
                        ]
                    ];
                    break;
                case 'heading':
                    $block = [
                        'type' => 'heading',
                        'fields' => [
                            'text' => $this->faker->text(40),
                            'htmlTag' => 'h2'
                        ]
                    ];
                    break;
                case 'quote':
                    $block = [
                        'type' => 'quote',
                        'fields' => [
                            'text' => $this->faker->text(80),
                            'cite' => $localFaker->name,
                            'style' => $this->faker->randomElement(['', 'colored', 'bordered'])
                        ]
                    ];
                    break;
                case 'image':
                    $image = $this->getRandomImage(900);
                    $block = [
                        'type' => 'image',
                        'fields' => [
                            'image' => $image ? [$image->id] : null,
                            'caption' => $this->faker->text(30),
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
                            'align' => 'wide',
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

    protected function getStoryContent(int $index): array
    {

        $localFaker = Factory::create('de_DE');

        $content = [
            'sortOrder' => [],
            'blocks' => []
        ];

        $folder = Craft::$app->assets->findFolder(['path' => 'starter/']);

        $layouts = [
            ['titleBlock', 'cover', 'cover', 'text', 'image', 'text', 'heading', 'text', 'cover'],
            ['cover', 'cover', 'cover', 'titleBlock2', 'text', 'heading', 'text', 'cover', 'cover', 'image', 'image', 'text'],
            ['titleBlock', 'text', 'heading', 'text', 'cover', 'text', 'image', 'image']
        ];

        // $blockTypes = $this->faker->randomElement($layouts);
        $blockTypes = $layouts[$index - 1];

        $i = 0;
        foreach ($blockTypes as $blockType) {

            switch ($blockType) {
                case 'text':
                    $block = [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->getMarkdownParagraphs($this->faker->numberBetween(3, 5))
                        ]
                    ];
                    break;
                case 'heading':
                    $block = [
                        'type' => 'heading',
                        'fields' => [
                            'text' => $this->faker->text(40)
                        ]
                    ];
                    break;
                case 'titleBlock':
                    $block = [
                        'type' => 'titleBlock',
                        'fields' => [
                            'template' => 'fullheight.twig',
                        ]
                    ];
                    break;
                case 'titleBlock2':
                    $block = [
                        'type' => 'titleBlock',
                        'fields' => [
                            'template' => 'textonly.twig',
                        ]
                    ];
                    break;
                case 'image':
                    $image = $this->getRandomImage(900);
                    $block = [
                        'type' => 'image',
                        'fields' => [
                            'image' => $image ? [$image->id] : null,
                        ]
                    ];
                    break;
                case 'cover':
                    $image = $this->getRandomImage(1800);
                    $block = [
                        'type' => 'cover',
                        'fields' => [
                            'image' => $image ? [$image->id] : null,
                            'text' => $this->faker->text(50),
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
    public function actionImgAddProvisionalTexts(string $path = 'starter/', string $defaultCopyright = ''): int
    {
        $folder = Craft::$app->assets->findFolder(['path' => $path]);
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

        if ($this->interactive && !$this->confirm("Add provisional alt text/copyright to images?", true)) {
            return ExitCode::OK;
        }

        $localFaker = Factory::create('de_DE');

        $photographers = [];
        for ($i = 0; $i < 6; ++$i) {
            $photographers[] = $localFaker->name;
        }

        foreach (Craft::$app->sites->allSites as $site) {
            $images = Asset::find()
                ->kind('image')
                ->folderId($folder->id)
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
                    $image->copyright = $defaultCopyright ?: $localFaker->randomElement($photographers);
                    $save = true;
                }
                if ($save) {
                    $this->stdout("Saving provisional alt text / copyright to $image->title ($site->name)" . PHP_EOL);
                    Craft::$app->elements->saveElement($image, false, true, false);
                }
            }
        }
        return ExitCode::OK;
    }

    protected function getRandomTopic(): ?Entry
    {
        return Entry::find()
            ->section('topic')
            ->orderBy(Craft::$app->db->driverName === 'mysql' ? 'RAND()' : 'RANDOM()')
            ->one();
    }

}