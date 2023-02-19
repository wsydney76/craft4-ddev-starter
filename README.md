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


### Starter content

* Some starter images (copyright: Pixabay) are included.
* More example images can be downloaded from Unsplash via `ddev craft main/seed/create-images [number]`. If it doesn't time out...

Running `craft main init` will

* Ask for site name, email settings, copyright
* Create basic pages, like homepage, news index, sitemap, about/contact, search, legal stuff
* Create a second user assigned to `Content Editors` group.
* Assign user photos.
* Add provisional alt text/copyright to images (so that there will be no validation errors when doing the first demo...)
* Create a number of 'News' entries
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
* Added config/redactor/Custom.json as a safe Redactor config
* Added code to prevent password managers like Bitdefender Wallet from falsely inserting credentials into user form
* Added code to prevent creating search index for drafts/revisions
* Added code for better validation on multi site installs
* Added frontend tooling that uses Tailwind CSS, Alpine JS, Baguettebox (Lightbox) via Vite/Craft Vite plugin
* Added custom css for the control panel
* Added plugins: Contact Form, Element Map (custom), Content Overview (custom, PoC, disable by default) Imager X (optional), SEO Mate, Sprig, Vite, Code Field

## Custom Config

This starter comes with a mix of functionality that is likely to be used in every project.

### Sections

* Pages section: We don't use singles, instead dedicated entry sections. Main navigation shows children of the homepage.
    * Home page. One page with slug `__home__` required.
    * News Index: paginated card view of news entries
    * Topic Index: displays topic hierarchy
    * Contact: Simple contact form with first party Contact Form plugin
    * Sitemap
    * Search powered by Sprig
    * Default: just heading and blocks
    * Nav item: Does not have its own content, it creates a primary navigation item that lists its children in a dropdown, or renders a custom template in the dropdown panel.
    * Page Template: Create a page that renders a custom page template, e.g. for more specific section indexes with eager loading enabled. This is also the place where plugins can install their own pages.
* News section
* Topic section
* Person section
    * Default: Just photo, name, job description, social links. This is used in content components like testimonial, team, where details do not matter. Does not have its own page.
    * Profile: More details, like short bio, works. Plugins can use this type, e.g. for an actress profile, and provide their own frontend pages.
* Legal section. Use for privacy, imprint. Shown in footer navigation
* 'Content Section' Embedded section with Features/Team/Testimonial types. 
* Hero Area: Embedded section. This allows hero area/CTA content which can be scheduled precisely, independent of the owning entry.

### More content

* Content Builder field with block types: Heading, Text, Image, Gallery, Quote, Cards, YouTube Video, Dynamic content with custom template, content sections (section page only)
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
* fading: fading image
* split: text on the left, image on the right. Great for longer texts.

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

Different component types are implemented as entry types, which prevents the entries index from getting bloated.

The starter contains the following examples, mainly derived from Tailwind UI.

* Features: Lists some features with icon, header, text
* Team: List of team members. Uses the 'person' section
* Testimonial: A single testimonial. Uses the 'person' section.
* Cards: Shows entries in a cards layout with custom criteria, e.g. a 'Latest News' section.
* Two colomns with image: Column layout with and image and the default content builder.
* FAQs: An accordion component with questions and, hopefully, answers.

You can also select a hero area, this way it can be included anywhere on the page.

Using this in a production environment requires optimization in styling, using correct responsive image sizes, performance (eager loading things).

The search is provisionally prepared for this, but correct search results may be missing.

Also previewing pages with embedded entries is limited, what should work for now:

* Edit embedded content section in a slidout, do not save!
* Close slideout, confirm if asked.
* Press `Refresh`

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

__... do you seed the database via scripts and don't simply ship a database dump?__

* It is some work to have a db at hand without any test content.
* We want a more granular control over what will be created.
* It is easier to version control and share.
* It should be independent of the database type and version you use.
* We need this scripts anyway for plugins to create their things.

__... don't you use single sections?__

* Because we can arrange pages from different entry types in a hierarchy.
* A 'single' navigation item is not very intuitive for users.

__... don't you use categories but sections for taxonomies?__

* We want to give users a unified editing experience.
* We want to group together what belongs together. E.g. A 'genre' is shown beneath 'film'.
* Sometimes it is not clear what is a 'category' and what not. Is a 'country' a film is produced in a category or not?
* We want to resolve hierarchy at run time, instead of having redundant relationships. 
* All content is available under only one main navigation point.

__... do you use this strange Tailwind CSS bloat?__

* It is such a productivity boost.

__... don't you use at least Tailwinds @apply to avoid those endless chains of classes?__

* Do not mix things up.
* Avoid repetition by building components.

__... don't you use images transforms defined in the CP?__

* We think of image sizes as part of the design, so it feels more natural to define them in the frontend templates along with the markup they live in.
* Or define them in `config/custom.php` if used in multiple places.
* Version control them along with the templates that use them without affecting the project config.
* Sizes can be calculated dynamically depending on user selections (alignment, aspect ratio).

__... do you place the root folder for images outside the web root?__

* This way we enforce that only transformed, and therefore smaller, images are used on the front end.
* This way we avoid that the original images can be downloaded by simply manipulating the URL. 
* Which is important if you want to make them available only for specific users, like print ready press photos.
* Or make them paid content, e.g. on Patreon.

__... do you use the Imager-X plugin instead of using the native image transforms?__

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


__... is the stuff on this account in a semifinished state, to say the least?__

* The developers in the core team are usually caught up in the day-to-day business and find little time and concentration to develop and try out new ideas.
* It has therefore worked well for us to have such things done outside the core team by people who are not involved in daily business.
* And then turn it over to the core team that will clean up and polish things and make it look 'professional' and work reliably.

__... is this such lousy english?__

* Sorry, we don't have native speakers on the team, or someone who has lived in an English-speaking country.
* And you see what happens when you only interact with other non-native speakers. Mistakes multiply.


## Money

Craft CMS Pro edition and the Imager X plugin are paid software, but can be used for free on non-production domains.

It is safe to switch to Craft CMS Solo Edition and remove the Imager X plugin. This starter will continue to work.

Contains templates derived from [Tailwind UI](https://tailwindui.com/) and [Alpine UI Components](https://alpinejs.dev/components).

Even though we created everything owning valid licenses, it is up to you to comply with the license terms when you use this starter for your own project.

Contact the vendors if in doubt.