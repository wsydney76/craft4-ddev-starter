# Craft 4 Simple Starter

Multilingual Craft CMS starter for use with (or without) DDEV with a simple config and opinionated templating conventions, Can be useful as a starting point.

This is the result of a girls-only internship project, and we thank Aylin and Mel for bringing it to life and making it a success.

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
  * `cp .env.example.env`
  * `php craft setup/welcome` Enter your full URL when asked for 'Site URL'.
  * `php craft main/init`


### Sample content

* Add some images (min 1200 px wide). Some starter images (copyright: Pixabay) are included.
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
* Added plugins: Contact Form, Element Map (custom), Imager X (optional), SEO Mate, Sprig, Vite

## Custom Config

This starter comes with a mix of functionality that is likely to be used in every project.

* Pages section: We don't use singles, instead dedicated entry sections. Main navigation shows children of the homepage.
  * Home page
  * News Index: paginated card view of news entries
  * FAQs: FAQs with Alpine JS component
  * Contact: Simple contact form with first party Contact Form plugin
  * Sitemap
  * Search powered by Sprig
  * Default: just heading and blocks
* News section
* Legal section. Use for privacy, imprint. Shown in footer navigation
* Hero Area: Embedded section. This allows hero area/CTA content which can be scheduled precisely, independent of the owning entry.
* Content Builder field with block types: Heading, Text, Image, Gallery, Quote, Cards, YouTube Video
* Site Info global inc. contact info
* Assets with translatable alt text, copyright field
* SEO via Seomate plugin inc. Json-ld meta data
* Has Drafts condition rule for entry index: Shows my open drafts
* Has Empty Alt Text condition rule for asset index
* Volumes/file systems. Images volume outside of web root, so transformed images are forced on front end. No use of named image transforms.
* Custom config file inc. transform settings
* Tailwind CSS config with named colors


## Templating
Layout inspired by Craft Quest Real World CMS course templates

Folders:

* _layouts: Templates that define the general look and feel of the site, mostly independent from the actual content model.
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

