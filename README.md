# Craft 4 Simple Starter

Multilingual Craft CMS starter for use with (or without) DDEV with a simple config and opinionated templating conventions, can be useful as a starting point.

This is initially the result of a girls-only internship project, and we thank Aylin and Mel for bringing it to life and making it a success.

Still work in progress, trying to make it suit for

* quick demos for potential customers
* hobby or semi-professional projects
* developer training
* prototyping new stuff
* playground for internships and student theses
* basis for ongoing PoCs, that try to split existing functionality into a series of plugins.

## Install

* Make sure DDEV is installed
* Clone this repository `git clone https://github.com/wsydney76/craft4-ddev-starter <dir> && cd <dir>`

### Using DDEV (recommended)

* Run `bash setup/install`. This will create a user with the credentials `admin/password`.

Note: DDEV is configured here with http-port=81 in order to avoid a conflict with a web server running on the host machine (which in turn should not use 443 for https).
Edit `setup/install` to change this.

### Without DDEV

In case you do not want to use DDEV:

* Set up a development environment as you are used to, incl. database and web server
* Php 8.1 and Node js V16 required
* Run
    * `composer install`
    * `npm install`
    * `npm run build`
    * `cp .env.example .env`
    * `php craft setup/welcome` Enter your full URL when asked for 'Site URL'.
    * `ddev craft index-assets/one images`
    * `php craft main/init`


### Sample content

* Add some images (min 1200 px wide). Some starter images (copyright: Pixabay) are included.
* More example images can be downloaded from Unsplash via `ddev craft main/seed/create-images [number]`. If it doesn't time out...
* Run `ddev craft main/init` (or `php craft main/init` if not on DDEV)

## System

* Added modules/_faux to enable autocompletion for some most frequently used variables in twig
* Adds main module modules/main
    * Adds main/init and main/seed console controllers for creating starter/sample content
    * Adds main/assets console controller for asset transforms helper functions
* Set system timezone to Europe/Berlin
* Added /web/cpresources, /node_modules to .gitignore
* Added some settings to config/general.php
* Added config/redactor/Custom.json as a safe Redactor config
* Added code to prevent password managers like Bitdefender Wallet from falsely inserting credentials into user form
* Added code to prevent creating search index for drafts/revisions
* Added code for better validation on multi site installs
* Added frontend tooling that uses Tailwind CSS, Alpine JS, Baguettebox (Lightbox) via Vite/Craft Vite plugin
* Added custom css for the control panel
* Added plugins: Contact Form, Element Map (custom), Content Overview (custom, PoC) Imager X (optional), SEO Mate, Sprig, Vite

## Custom Config

This starter comes with a mix of functionality that is likely to be used in every project.

* Pages section: We don't use singles, instead dedicated entry sections. Main navigation shows children of the homepage.
    * Home page. One page with slug `__home__` required.
    * News Index: paginated card view of news entries
    * FAQs: FAQs with Alpine JS component
    * Contact: Simple contact form with first party Contact Form plugin
    * Sitemap
    * Search powered by Sprig
    * Default: just heading and blocks
    * Nav item: Does not have its own content, it creates a primary navigation item that lists its children in a dropdown, or renders a custom template in the dropdown panel.
    * Section Index: Create a simple index page for new sections.
    * Page Template: Create a page that renders a custom page template, e.g. for more specific section indexes with eager loading enabled.
* News section
* Legal section. Use for privacy, imprint. Shown in footer navigation
* Hero Area: Embedded section. This allows hero area/CTA content which can be scheduled precisely, independent of the owning entry.
* Content Builder field with block types: Heading, Text, Image, Gallery, Quote, Cards, YouTube Video, Dynamic content (custom template)
* Site Info global inc. contact info
* Assets with translatable alt text, copyright field
* SEO via Seomate plugin inc. Json-ld meta data
* Has Drafts condition rule for entry index: Shows my open drafts
* A 'my provisional drafts' dashboard widget.
* Has Empty Alt Text condition rule for asset index
* Volumes/file systems. Images volume outside of web root, so transformed images are forced on front end. No use of named image transforms.
* Custom config file inc. transform settings
* Tailwind CSS config with named colors
* Dark mode switch for templates
* A `BaseModule` that provides common functionality like creating entries, fields, sections etc.


## Templating

Folders:

* _layouts: Templates that define the general look and feel of the site, mostly independent of the actual content model.
    * components: Sprig/Alpine JS components like search, accordion, scroll-to-top button
    * embeds: Layout components, incl. page content layouts(single colum wide/narrow, sidebar), cards, widgets etc. Dark mode switch for front end
    * macros: img macro for advanced image handling, prepared for Imager-X plugin
    * partials: includes for hero area, page headers, buttons etc.
* \_sections/[sectionHandle], \_sections/[sectionHandle]_[typeHandle]: Section/Type specific templates. The following templates will be called if present in the folder:
    * card-content.twig: Section/type specific content for cards. Use card-text embed for consistent layout.
    * hero-area.twig: Outputs a section/type specific hero area
    * json-ld.twig: Outputs section/type specific Json-ld meta data
* _blocks: Content builder block type templates
* _errors: Templates for error pages
* _macros: Project specific macros
* _partials: Project specific partial templates
* _widgets: Widget example for sidebar layouts

Templates are prepared to use rounded corners for cards, images etc., this is by default switched off in `tailwind.config.js`. Uncomment the `borderRadius` settings to see rounded corners.

## Hero Area

The hero area shows:

* a background image with copyright
* a teaser line above the title (optional)
* a title
* a text (optional). Rendered as markdown
* a call-to-action button (optional)

Additionally  `topHtml` and `bottomHtml` params can be passed in that will appear, you guessed it, at the top or bottom. 


The hero area should be rendered via `_layouts/partials/hero-area-display.twig`.

The actual template lives in `_layouts/partials/heroarea` and is determined

* by the `heroAreaTemplate` field value of a hero area entry, if available
* by a `template` parameter
* by the `heroTemplate` setting in `config/custom.php`

The `default` template is prepared for this starter. 

Some hero templates stolen from other projects are included as a demo, styling needs to be optimized, image transforms should be adjusted.

* textonly: No image, this is by default also used as a fallback if no image is present.
* imagetext: image + textonly below
* imagecoloredbg: image + text on a colored background
* introimage: colored background with an overlapping image
* skew: image with a semi transparent, gradient overlay.
* textimagehalfed: text on the left, image on the right. Great for longer texts.

Unused templates should be deleted in order to avoid unused tailwind classes in your css.

Content is determined in the following order:

* the entry template overwrites the `heroArea` twig blog
* a `_sections/#{entry.section.handle}_#{entry.type.handle}/hero-area.twig` template exists
* a `_sections/#{entry.section.handle}/hero-area.twig` template exists
* the current entry links to `Hero Area` entries. The first 'live' hero area entry will be displayed, this enables you to play with site specific or temporary hero areas, setting enabled/post date/expires. Remember to refresh cached pages... 
* the current entry links to a `Featured Entry`.

## Components

### Layout components

tbd. In the meantime see `_layouts/embeds, _layouts/embeds`.


### Macros

tdb. In the meantime see `_layouts/macros`.

Always use the `img` macro for images!

### Alpine.js components

tbd. In the meantime see `_layouts/components/alpinejs`.

Includes a number of components that are not used in this starter, but can be useful for a better user experience.


## Customization

Update templates as you like.

* Run `npm run dev (automatic reloading)/npm run build` when changing tailwind stuff (config/classes in templates).

Settings worth mentioning:

### config/custom.php

See comments for possible values.

* `'stickyMenu' => true,` for sticky navigation
* `'heroWidth' => 'xl',` if you do not want a full width hero area
* `'navWidth' => 'xl',` Width of primary navigation header
* `'mobileNavBreakpoint' => 'md',` When to show the mobile (hamburger) menu. 
* `'heroTemplate' => 'skew',` The template for the hero area. See below.

### tailwind.config.js

Templates use named colors (except `gray`), like `primary`. As a starting point default Tailwind colors are used.

* change color values to match your custom color schema.
* `hasDarkHeader = false / hasDarkFooter = false` if you do not want a dark background for header/footer
* set `borderRadius` values to 0 if you do not want to use rounded corners for images

### Fonts

Fonts are hosted locally, Google fonts can be downloaded from [https://gwfh.mranftl.com/fonts](https://gwfh.mranftl.com/fonts).

`font-heading` is used for headlines.

* Place fonts in `web/assets/fonts`
* Update `fontFamily` in `tailwind.config.js`
* Update `resources/css/fonts/fonts.css`. Make sure `font-display: swap;` is defined.
* Update `preloadFonts` in `config/custom.php` for fonts used above the fold.

### Social Icons

Links to social networks can be edited via the `Site Info` global.

Add missing networks:

* Add icon to `templates/_icons`
* Update field `socialLinks`, the dropdown value must match the filename.

### Icons/Branding

* Replace the icons in `/web`.
* Update `templates/_layouts/head` and/or `web/sitemanifest.json` if you have different sizes/filenames.

## Not in here

What the starter does not do:

* Optimize SEO, Json-Ld
* User tracking and cookie banners (the YouTube block is safe to use without it)
* Spam protection
* Advanced caching. A sample config file for the Blitz plugin is included.
* Trying to achieve near perfect code quality.

## Money

Craft CMS Pro edition and the Imager X plugin are paid software, but can be used in non-production domains.

It is safe to switch to Craft CMS Solo Edition and remove the Imager X plugin. This starter will continue to work.