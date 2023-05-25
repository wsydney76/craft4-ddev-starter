# Craft 4 Starter

The Multilingual Craft CMS Starter is designed to function with or without DDEV. It features a simplified configuration and employs distinct templating conventions. It's useful as a basis for numerous tasks.

The development of this starter kit started during an all-female internship project. Credit goes to Aylin and Mel who have significantly contributed to its creation and successful completion.

While still in its beta phase, the CMS starter is already feature complete. It's been developed with various applications in mind:

* Quick demos for potential clients
* Hobby or semi-professional projects
* Developer training
* Prototyping
* Internships and student theses
* Basis for ongoing PoCs aiming to modularize existing functionality into plugins.

## Versions

Tagged a provisional 3.0 version for Craft 4.4, use 2.1.0 for Craft 4.3. 

## Disclaimer

Although shared publicly, this project was created for internal use, and therefore contains things that seem a bit strange without context.

## Install

* Make sure DDEV is installed
* Clone this repository `git clone https://github.com/wsydney76/craft4-ddev-starter <dir> && cd <dir>`

### Using DDEV (recommended)

Run `bash setup/install <project-name>`. 

This will 

* configure DDEV
* let composer install the packages (Craft CMS and plugins).
* install Craft with a user with the credentials `admin/password`. 
* index included starter images
* install npm dependencies
* build frontend assets (css/js)
* run `main/init` (see Starter content below)

Note: DDEV is configured to use the default ports 80/443. If you have other services running on these ports, you will have to adjust the port mapping in `.ddev/config.yaml` and the `PRIMARY_SITE_URL` setting in `.env` accordingly. 

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


### Starter content

* Some starter images (copyright: Pixabay) are included.
* More example images can be downloaded from Unsplash via `ddev craft main/seed/create-images [number]`. If it doesn't time out...

Running `craft main init` will

* Ask for site name, email settings, copyright
* Create basic pages, like homepage, article index, sitemap, about/contact, search, legal stuff
* Create a second user assigned to `Content Editors` group.
* Assign user photos.
* Add provisional alt text/copyright to images (so that there will be no validation errors when doing the first demo...)
* Create a number of 'Article' entries
* Create some random homepage content (fun fact: the main image shows the place where the idea for this starter was born)
* Retrieve each page so that image transforms are created

## System

* Added modules/_faux to enable autocompletion for some most frequently used variables in twig
* Adds main module modules/main
    * Adds main/init and main/seed console controllers for creating starter/sample content
    * Adds main/assets console controller for asset transforms helper functions
* Set system timezone to Europe/Berlin
* Added /web/cpresources, /node_modules to .gitignore
* Added some settings to config/general.php
* Added config/htmlpurifier/Custom.json as a safe HTMLPurifier config
* Added config/redactor/Custom.json as a safe Redactor config
* Added config/project/ckeditor/Custom.json as a safe CKEditor config
* Added code to prevent password managers like Bitdefender Wallet from falsely inserting credentials into user form
* Added code to prevent creating search index for drafts/revisions
* Added code for better validation on multi site installs
* Added frontend tooling that uses Tailwind CSS, Alpine JS, Baguettebox (Lightbox) via Vite/Craft Vite plugin
* Added custom css for the control panel
* Added extended 'Copy reference tag' element actions that generates output which can be used in markdown fields to create links.
* Added plugins: Contact Form, Element Map (custom), SEO Mate, Sprig, Vite, Code Field
* Prepared for CKEditor plugin (not install by default).

## Custom Config

This starter comes with a mix of functionality that is likely to be used in every project.

### Sections

* Pages section: We don't use singles, instead dedicated entry sections. Main navigation shows children of the homepage.
    * Home page. One page with slug `__home__` required.
    * Article Index: paginated card view of article entries
    * Topic Index: displays topic hierarchy
    * Contact: Simple contact form with first party Contact Form plugin
    * Sitemap
    * Search powered by Sprig
    * Default: just heading and blocks
    * Nav item: Does not have its own content, it creates a primary navigation item that lists its children in a dropdown, or renders a custom template in the dropdown panel.
    * Page Template: Create a page that renders a custom page template, e.g. for more specific section indexes with eager loading enabled. This is also the place where plugins can install their own pages.
* Article section
* Topic section
* Person section
    * Default: Just photo, name, job description, social links. This is used in content components like testimonial, team, where details do not matter. Does not have its own page.
    * Profile: More details, like short bio, works. Plugins can use this type, e.g. for an actress profile, and provide their own frontend pages.
* Legal section. Use for privacy, imprint. Shown in footer navigation
* 'Content Section' Embedded section with Features/Team/Testimonial types. 
* Hero Area: Embedded section. This allows hero area/CTA content which can be scheduled precisely, independent of the owning entry.

#### Article? News? Blog? Post?

In this starter, the term 'article' is used to denote a channel. The decision to avoid terms like 'blog'/'news' or 'post' was made due to their specificity or generality, respectively. 'Article' offers a more neutral alternative that can accommodate various content types.

To display a different term to your users, it's recommended to modify the name/uri in the section settings while leaving the handle unchanged. This approach ensures the compatibility of the existing code/settings without necessitating alterations.

However, this will require making adjustments to the translations. It's important to keep this in mind while using the starter.

### More content

* Content Builder field with block types: Heading, Text, Image, Gallery, Quote (with style variations), Cards, YouTube Video, Dynamic content with custom template, content sections (section page only)
* Site Info global inc. contact info
* Assets with translatable alt text, copyright field

### Other

* SEO via Seomate plugin inc. Json-ld meta data
* Has Drafts condition rule for entry index: Shows my open drafts
* A 'my provisional drafts' dashboard widget.
* Has Empty Alt Text condition rule for asset index
* Volumes/file systems. Images volume outside of web root, so transformed images are forced on front end. No use of named image transforms.
* Custom config file inc. transform settings
* Tailwind CSS config with named colors
* Dark mode switch for templates
* A `BaseModule` that provides common functionality like creating entries, fields, sections etc.

## Custom Field Types

### Environment Variable Field

Lets you edit values in your `.env` file in the CP, used in Global sets. Will be useful if you don't have access to the `.env` file.

Use field conditions to restrict access to field instances to admins.

### Include Field

Lets you select a template from a configured folder. Can be useful for selecting different design variations.

### Site Field.

Lets you select a site.

## Templating

Folders:

* _layouts: Templates that define the general look and feel of the site, mostly independent of the actual content model.
    * components: Sprig/Alpine JS components like search, accordion, scroll-to-top button
    * embeds: Layout components, incl. page content layouts(single colum wide/narrow, sidebar), cards, widgets etc. Dark mode switch for front end
    * macros: img macro for advanced image handling, prepared for Imager-X plugin
    * partials: includes for hero area, page headers, buttons etc.
    * contentsections: a bit more complex layouts for components like features/team/testimonial
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
* fading: fading image (by Tailwind UI)
* split: text on the left, image on the right. Great for longer texts.
* HeaderWithBackgroundImage: image with a semi transparent, gradient overlay. The image is used as a background image for the header. (by Tailwind UI)

Unused templates should be deleted in order to avoid unused tailwind classes in your css.

Content is determined in the following order:

* the entry template overwrites the `heroArea` twig blog
* a `_sections/#{entry.section.handle}_#{entry.type.handle}/hero-area.twig` template exists
* a `_sections/#{entry.section.handle}/hero-area.twig` template exists
* the current entry has `Show Hero Area` switched on.
* the current entry links to `Hero Area` entries. The first 'live' hero area entry will be displayed, this enables you to play with site specific or temporary hero areas, setting enabled/post date/expires. Remember to refresh cached pages... 

## Content Components (experimental)

This is mainly for spinning up quick (potential) customer demos.

The 'Homepage' page entry type and the 'Content Components' body content block have a 'Content Sections' entries field, which allows you to compose your page with content living in separate entries from the Hero Area or 'Content Section' sections.

The starter contains the following examples, mainly derived from Tailwind UI.

* Features: Lists some features with icon, header, text
* Team: List of team members. Uses the 'person' section
* Testimonial: A single testimonial. Uses the 'person' section.
* Cards: Shows entries in a cards layout with custom criteria, e.g. a 'Latest Articles' section.
* Two colomns with image: Column layout with and image and the default content builder.
* FAQs: An accordion component with questions and, hopefully, answers.
* Newsletter: A newsletter signup form. Requires the 'Emaillist' custom plugin to be installed.

You can also select a hero area, this way it can be included anywhere on the page.

Using this in a production environment requires optimization in styling, using correct responsive image sizes, performance (eager loading things).

The search is provisionally prepared for this, but correct search results may be missing.

Also previewing pages with embedded entries is limited, what should work for now:

* Edit embedded content section in a slideout, do not save!
* Close slideout, confirm if asked.
* Press `Refresh`

## Topics (Categories/Taxonomies)

Includes a demo implementation of a 'topic' section as a taxonomy.

The use of taxonomies can be very different in terms of

* Number of taxonomies
* Number of entries per taxonomy
* Number of hierarchy levels 
* Number of related entries
* How to display relations (with or without hierarchy)
* What additional content taxonomies can have
* Whether an index page should be shown or not.

The starter implements a 3-level 'Topics' taxonomy that works best with a limit number of topics.

However, it is not very likely that this will meet your needs, but you may find some usefully techniques.

## Navigation

The main navigation uses the 'page' section hierarchy, the direct descendants of the 'homepage' entry will be shown as primary navigation items.

If you want a dropdown showing another level, 

* insert an entry of type `Nav Item` below the homepage and add subpages as children. This will create a consitent UX, where clicking a nav item (text or down arrow) will open a dropdown menu. Alternatively, you can specify a Twig template that can populate the dropdown panel as desired.
* set `showChildrenInMainNav = true` in `config/custom.php`. This will link the main navigation item to the level 2 page, clicking the down arrow will open a dropdown showing the level 3 pages. This has real life precedents and does not require a helper product, but the UX is less clear.

You should not mix these two possibilities

## Person section type

We have included a `Person` section type with two entry types:

__Default__

Just a name (title), job description photo, social links.

An `Article` entry can relate to multiple persons (= authors), if you do not want to show the CMS user as an author on the front end.

There is a `getAuthors()` entry behavior method, that return either data from person(s) entries or, as a fallback, the user element. 

While the native `author` field points to a CMS user and its permissions, you may want to display the person(s) who actually wrote the text. And user elements are not translatable.


Also, the `Team` and `Testimonial` content components have relationships to a `Person` in order to avoid redundancy.

__Profile__

A more detailed entry type that is not used by the starter itself, but is included to be used by plugins, e.g. for an actress profile.

Has more detailed name fields, content builder, profile fields like bio, birthday, works (e.g. filmography, books).

## Twig Components

Usage is (fingers crossed) described in comments in each component.

### Layout components

Worth mentioning:

__content-md.twig, content-xl.twig__

Use these embeds when you want to limit all your content to a specific width with a consistent page header.

__container-md.twig, container-xl.twig__

Use these embeds to limit a piece of your content to a specific width with consistent horizontal padding.

__content-sidebar.twig__

Use this embed for a sidebar layout.

__prose.twig__

Use this embed for a consistent typography.

__grid.twig__

Use this embed for a consistent grid layout.

__widget.twig__

Use this embed for a consistent widget layout inside the sidebar layout.

__card.twig__

Use this embed for a consistent card layout

__card-text.twig__

Use this embed for a consistent layout of content inside cards.

__button.twig__

Renders a button.

__headline.twig__

Renders a consistent headline.

__cover-image.twig__

Renders a cover image, i.e. a background image with a centered content block

### Macros

__forms.twig__

Some basic input fields, used for the contact page.

__lib.twig__

Always use the `img` macro for images!

### Alpine.js components

Reusable components that can be useful for a better user experience:

Components from [Alpine UI Components](https://alpinejs.dev/components) are especially valuable because they are built with accessibility in mind.

May need some adjustments.

* dropdown.twig: Dropdown component, used in navigation.
* accordion.twig: Accordion component, used in faqs.
* choices.twig: Select input via choices.js
* select2.twig: Select input via select2.js
* modal.twig: Modal window with trigger button.
* toast.twig: Give user feedback via a toast message.
* tabs.twig: Well, tabs.
* toggle.twig: A toggle boolean input field.
* splide.twig: A carousel component via splide.js

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
`borderRadius` values are set to 0. Uncomment this if you want to use rounded corners for images

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
* Progressive web app things.

## Plugins

This starter serves as a foundation for a series of PoC plugins that try to separate their functionality from a main project.

The plugins can use components/CSS from the starter, so these things need to stay compatible and strictly version controlled.

Currently available:

### Generic stuff

* [Members](https://github.com/wsydney76/craft-members): Membership stuff like registration, login, password reset etc.
* [Favorites](https://github.com/wsydney76/craft-favorites): Let a user select favorite entries.
* [Email list](https://github.com/wsydney76/craft-emaillist): Collect email addresses, e.g. for a newsletter

### Specific stuff

Implements the information model, CP setup and frontend for typical application areas:

The goal for the next winter season is 

* to have a common starter project 
* then install a specific plugin 
* continue customization from there

Currently available:

* [Film Festival Light](https://github.com/wsydney76/craft-film-festival-light)

Next on the list:

* theater companies
* music festivals
* artist agencies
* conferences
* museums

Not on the list:

* cinemas. Too much competition/ready solutions in this area...

## Why oh why ...

Some wonder why we built things the way we did. No pseudo-religious beliefs at all, just what feels better.

__... do you seed the database via scripts rather than simply providing a database dump?__

* Crafting a database without test content can be labor-intensive.
* Scripting allows more granular control over what is created.
* Scripts are simpler to version control and distribute.
* Scripts ensure independence from the database type and version in use.
* These scripts are also necessary for plugins to generate their respective components.

__... don't you use single sections?__

* A structure allow us to organize pages from various entry types into a hierarchical structure.
* A 'single' navigation item doesn't provide an intuitive user experience.

__... don't you use categories but sections for taxonomies?__

* Our goal is to provide users with a cohesive editing experience.
* We aim to logically group related items. For instance, a 'genre' is displayed under 'film'.
* Sometimes, it can be ambiguous whether a certain element is a 'category'. For example, is the 'country' where a film was produced considered a category?
* We prefer to establish hierarchy during runtime, which avoids redundant relationships.
* All content is conveniently accessible under a single main navigation point.

__... do you use this strange Tailwind CSS bloat?__

* It is such a productivity boost.

__... don't you use at least Tailwinds @apply to avoid those endless chains of classes?__

* Do not mix things up.
* Avoid repetition by building components.

__... do you add custom styles to the CP?__

* The gray sidebar makes me sad...
* We like to increase font-size and contrast.
* We use a custom resource bundle instead of a plugin, because it can be version controlled without changing the project config. And one plugin less.

__... don't you use images transforms defined in the CP?__

* We consider image sizes to be integral to the design, hence it's more intuitive to define them in the frontend templates, where they are embedded.
* Alternatively, define these sizes in `config/custom.php` if they are utilized in multiple locations.
* Implement version control in conjunction with the templates that utilize these dimensions, without impacting the project configuration.
* Image sizes can be dynamically adjusted based on user choices (such as alignment, aspect ratio, etc.).o).

__Why isn't a rich text field like Redactor/CKEditor used?__

* We strive to maintain a high degree of control over the appearance of our site, aiming for a clean, minimal look and feel.
* The objective is to prevent users from adding arbitrary HTML elements.
* The websites we construct typically don't include large chunks of text.
* Similarly, we wish to stop users from inserting random image sizes.
* Therefore, even for markdown, we only permit a limited selection of HTML elements.
* While the Redactor/CKEditor field type can be tailored to only permit what we want, its benefits are somewhat limited in such a restrictive setting.
* In case Redactor/CKEditor is needed, we've included a `/config/redactor/Custom.json` configuration file and CKEditor configs.
* The use of Redactor/CKEditor also introduces the need for an additional plugin.
* Rich text editors embed media content like YouTube videos in a way that is not GDPR-compliant.
* If Redactor/CKEditor is deemed necessary, the `text` matrix block type field can be set to the Redactor/CKEditor field. Additionally, a `craft main/init/convert-text-blocks` command is available to convert existing content.

__... do you place the root folder for images outside the web root?__

* This way we enforce that only transformed, and therefore smaller, images are used on the front end.
* This way we avoid that the original images can be downloaded by simply manipulating the URL. 
* Which is important if you want to make them available only for specific users, like print ready press photos.
* Or make them paid content, e.g. on Patreon.

__... do you recommend to use the Imager-X plugin instead of using the native image transforms?__

* It is faster.
* Does not require to eager load transform records.
* Enables a clear separation between transformed images in the CP and on the frontend.
* It is more powerful, for example we always apply the 'sharpen' effect for better quality images by default.
* However, we always output images via an `img` macro, which can determine whether the plugin is installed or not and act accordingly.

__... do you have a 'Show in sites' field in image matrix blocks instead of using the 'Manage relations on a per-site basis' option?__

* Different images for different sites are a rare exception, so changing an image should not force the editors to change it on all sites by default.
* Could also be different block types, e.g. 'global image' vs 'site-specific image', but there is currently no way to convert one block type into another.

__... do you miss eager loading opportunities?__

* We overlooked them.
* There may be cases where eager loading things may not really help. 
* For example, in a content builder you do not know in advance which elements are used on a page and in what numbers. The 'n' in 'n+1' will be often 0.
* In general, we follow the rule that each page has to be fast enough for a single user. And then rely on aggressive caching with server rewrites to handle the traffic.

__... don't you use CDNs for assets?__

* The sites in mind are typically in-person events, so there is no need to serve assets from everywhere in the world.
* We avoid privacy issues by hosting everything locally, incl. Google fonts. Hello DSGVO!

__... is there no cookie banner?__

* No need to have one. There are no cookies, except for technically necessary session cookies.

__... don't you build a fancy headless solution?__

* For the type of projects this is used for, I can't see any advantage to do so.
* However, it may make sense to add an api that enables third party apps like event aggregators to retrieve data.

__... pff, you are just an old man unable to learn new things!!!__

* Yes I am old. That's why I have learned so many things and seen so many things come and go. And I have already forgotten more than these young people ever learned. (Sorry. Internal running gag.)
* Being serious for a moment: I strongly believe that it is not technology that makes a project fail or succeed. It's more about your client relationship, your processes, your skills, your empathy, your creativity, your resources.

__... don't you reveal the name of the agency this is made for?__

* We are asked to do so.
* The people there are under an enormous workload and have no free capacity.
* Imagine a full service event agency where building websites is only a small part.
* Therefore, building websites is not a selling point.

__... why Craft CMS at all?__

* The relationship engine. 
* Multisite capabilities.
* Live preview.


__... is the stuff on this account in a semifinished state, to say the least?__

* The developers in the core team are usually caught up in the day-to-day business and find little time and concentration to develop and try out new ideas.
* It has therefore worked well for us to have such things done outside the core team by people who are not involved in daily business.
* And then turn it over to the core team that will clean up and polish things and make it look 'professional' and work reliably.

__... is this such lousy english?__

* Sorry, we don't have native speakers on the team, or someone who has lived in an English-speaking country.
* And you see what happens when you only interact with other non-native speakers. Mistakes multiply.
* Some texts are also machine translated and/or improved by AI. We are not proud of it, but it is what it is.


## Money

Craft CMS Pro edition and the Imager X plugin are paid software, but can be used for free on non-production domains.

* This starter is configured to work with Craft CMS Pro edition; this way we can ship some advanced configuration, like User groups and additional preview targets. You should switch to Craft Solo edition if you do not need these features.
* The Imager X plugin is not installed by default.

This starter will continue to work, regardless of whether you use Craft Pro/Imager-X or not.

Contains templates derived from [Tailwind UI](https://tailwindui.com/) and [Alpine UI Components](https://alpinejs.dev/components).

Even though we created everything owning valid licenses, it is up to you to comply with the license terms when you use this starter for your own project.

Contact the vendors if in doubt.

## CKEditor

We still do not recommend using a rich text editor, but just in case we prepared some things...

* Install the CKEditor plugin: `ddev composer require craftcms/ckeditor -w && ddev craft plugin/install ckeditor`
* In `Fields` > `Body Content` > `Text` matrix block, change the field type to `CKEditor`, select `Custom` as the editor config and `Default` as the HTMLPurifier config, and save.
* Run `ddev craft main/init/convert-text-blocks` to convert existing content.
* Check the `Custom` config settings and adjust them to your needs.
* Check the `_blocks/text.twig` template and select the preferred way of handling oembed tags (in case you enabled them).
* Currently only YouTube videos are supported.
* In case things do not work as expected, check the HTML Purifier config first.
* Having a lot of matrix blocks with CKEditor may slow down editing, so you may want to enable your config to use headings/images/quotes. Which in turn will make it more difficult to sync content between sites. 

### Tables

If you want to use CKEditor just for its support of tables:

* Create a `bodyContent` block type with the handle `table` and a field with the handle `table`.
* Select CKEditor as field type and select `Table` as the editor config and `Table` as the HTMLPurifier config.