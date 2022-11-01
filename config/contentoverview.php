<?php

return [

    'navLabel' => 'Content Dashboard',

    'tabs' => [
        [
            'label' => 'Site',
            'id' => 'tab1',

            // width: span in a 12 columns grid
            'columns' => [
                [
                    'width' => 7,
                    'sections' => [
                        [
                            'section' => 'news',
                            'heading' => 'Latest News',
                            'limit' => 6,
                            'info' => '{tagline}, {postDate|date("short")}',
                            'imageField' => 'featuredImage',
                            'layout' => 'cards'
                        ]
                    ]
                ],
                [
                    'width' => 5,
                    'sections' => [
                        [
                            'section' => 'heroArea',
                            'limit' => 2,
                            'info' => '{heroTagline}',
                            'imageField' => 'heroImage',
                            'layout' => 'cardlets'
                        ],
                        [
                            'section' => 'page',
                            'heading' => 'Page Structure',
                            'info' => '{isDraft ? "Draft"} {type.name}',
                            'icon' => '@appicons/globe.svg',
                            'scope' => 'all'
                        ]
                    ]
                ],
                [
                    'width' => 4,
                    'sections' => [
                        [
                            'section' => 'legal',
                            'info' => '{type.name}'
                        ]
                    ]
                ]
            ],
        ],
        [
            'label' => 'News',
            'id' => 'tab2',
            'columns' => [
                [
                    'width' => 6,
                    'sections' => [
                        [
                            'heading' => 'Drafts',
                            'section' => 'news',
                            'limit' => 12,
                            'info' => [
                                '{tagline}',
                                '{postDate|date("short")}',
                            ],
                            'popupInfo' => [
                                Craft::t('site', 'Draft created by') . ' {creator.name}',
                                Craft::t('site', 'Draft created at') . ' {draftCreatedAt|date("short")}',
                                '{draftNotes ? "Draft Notes:"}',
                                '{draftNotes}'
                            ],
                            'imageField' => 'featuredImage',
                            'layout' => 'cardlets',
                            'orderBy' => 'postDate desc',
                            'scope' => 'drafts',
                            'buttons' => false,
                        ],

                        [
                            'heading' => 'My Provisional Drafts',
                            'section' => 'news',
                            'limit' => 12,
                            'info' => '{tagline}, {postDate|date("short")}',
                            'imageField' => 'featuredImage',
                            'layout' => 'cardlets',
                            'orderBy' => 'postDate desc',
                            'scope' => 'provisional',
                            'ownDraftsOnly' => true,
                            'buttons' => false
                        ],
                        [
                            'heading' => 'Pending',
                            'section' => 'news',
                            'limit' => 12,
                            'info' => '{tagline}, {postDate|date("short")}',
                            'imageField' => 'featuredImage',
                            'layout' => 'cardlets',
                            'orderBy' => 'postDate desc',
                            'status' => 'pending',
                            'buttons' => false
                        ],
                        [
                            'heading' => 'Disabled',
                            'section' => 'news',
                            'limit' => 12,
                            'info' => '{tagline}, {postDate|date("short")}',
                            'imageField' => 'featuredImage',
                            'layout' => 'cardlets',
                            'orderBy' => 'postDate desc',
                            'status' => 'disabled',
                            'buttons' => false
                        ],

                    ]
                ],
                [
                    'width' => 6,
                    'sections' => [
                        [
                            'heading' => 'Latest Live News',
                            'section' => 'news',
                            'limit' => 12,
                            'info' => '{postDate|date("short")}',
                            'imageField' => 'featuredImage',
                            'layout' => 'list',
                            'orderBy' => 'postDate desc',
                            'status' => 'live'
                        ],

                    ]
                ]
            ]
        ]
    ]
];