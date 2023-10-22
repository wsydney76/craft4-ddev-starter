# Changelog

## 3.4 2023-10-21

* Fixed a bug where the `hasDraftsConditionRule` condition was removed from the `Work/Drafts` element index source when `craft project-config/rebuild` was run.
* Added the `useCustomCrossSiteValidation` custom config setting.
* Bring back optional custom cross-site validation for entries, as the native solution in Craft 4.5 does not work in slideouts.
* Updated to Craft 4.5.7 and Sprig 2.7.2
* Added ECS (Easy Coding Standards) and Phpstan checks, according to [Craft's coding standards](https://craftcms.com/docs/4.x/extend/coding-guidelines.html).
* Images in the copyright footer are now link to the original image on the page, if the `handleCopyright` custom config is set to `register`.

## 3.3 2023-08-13

* Added `Writer` user group with limited permissions.
* Seed Testimonials/team components with better photos.
* Added 'Multiple Testimonials' component entry type.
* Fix image block default values.
* Added custom SEO preview, including a provisional Mastodon preview.
* Added SEO fields to `Article/Story` entry type.
* Fix SEO preview
* Update callactions.js for better error handling.
* Various minor tweaks and fixes.
* Updated Craft to 4.5.3.
* Removed custom `_globals` twig variable, as it's now native in Craft 4.5.
* Set new `Slug Translation Method` and `Show the Status field` entry type settings where appropriate.
* Added a hook to .ddev/config.yaml that creates a database dump on `ddev stop` or `ddev poweroff`.

### Todos

* Open CSS issue: horizontal shift on entry index page when refreshing the page.
* Provisionally fixed CSS issue: vertical shift on glabal header if a user photo is set.

## 3.2 2023-05-25

Updated for recent use cases. It's not been tested thoroughly, so use at your own risk.

* Update to the newest Craft 4.4
* Renamed `News` section to `Article`.
* Prepared for Staticcache plugin. (update nginx config and added example custom rules config)
* Improved use of semantic html5 tags.
* Removed unnecessary empty class attributes.
* Update custom html purifier config.
* Added `actionConvertTextBlocks` to convert `text` blocks markdown content to html, so that CKEditor can be used.
* Prepared CKEditor configs/templates for `text` and (not included) `table` blocks.
* Allowed table tags in `Custom` purifier config.
* Text blocks content is now output using the `extra` markdown flavor.
* The sprig plugin now hosts the hmtx library locally, so removed the `htmxScriptUrl` custom setting.
* Added the `prepareText` twig filter and corresponding custom settings.
* Add a preview target by default if on Craft Pro.
* Restrict uploadable file types to images and pdfs.
* Added Cookie Consent banner.
* Added `showLink` field to Legal section.
* Moved text modules to `textModule` section.
* Added `allowYoutubeVideos` custom config setting.
* Added the possibility to read starter content from files, via the `BaseController::getStarterTextFromFile()` method.
* Added 21:9 video aspect ratio
* Added `fetchJson/postAction` JavaScript functions
* Split `contentComponent` entry types into dedicated sections.
* Added preview for Content Component and Hero Area sections.
* Added `ImageTextOverlap` hero area template
* Added `Large Avatar` testimonial entry type

## 3.1.0 2023-04-06

* Update to the newest Craft 4.4
* Update to Tailwind 3.3.0
* Added `Align` setting to `gallery` block.
* Added `Custom` purifier config to `text` block.
* Added `redactor` and `htmlpurifier` configuration files from `craft/craft` repo.
* Streamlined main/init.
* Added bigger image columns to element index
* Added `Story` entry type for the `News` section.
* Improved display of copyright notices in footer.
* Added `_globals` twig variable, representing a Laravel collection that can be used to simulate a request scope (will be native in Craft 4.5).
* Added `customConfig` twig variable as alias for `craft.app.config.custom`.
* Prepared for rtl languages (needs review).
* Dropped environment variable fields from `Site Info` single entry field layout, as they do not support drafts. The fields are still available if you want to use them in a global set.
* Added `HeaderWithBackgroundImage` hero area template
* Renamed `quote` filter to `quotationMarks`
* Added `skipSrcset` custom config option to skip srcset generation for images in dev mode when not using Imager-X.
* Switch to Craft Solo license and dropped Imager-X plugin.
* Added better `copy reference tags` element actions.


## 3.0.1 2023-03-15

* Update to Craft 4.4.2
* Replaced the new Dumper app component for better readability and accessibility.
* Dropped custem d/dd twig functions
* Added a `showChildrenInMainNav` custom config setting. See Readme for usage.
* Fixed custom field types inputs with selectize to allow empty values.

## 3.0.0 2023-03-10

* Fixed minor layout problems.
* Added styles for block matrixblock type.
* Added some missing translations.
* Added some missing copyright notices for images.
* Allowed reference tags in `text` block types.
* Added a simple 'Guide' module (to be improved).

### Update to Craft 4.4.1

Just one day old, things may change/still have bugs...

* Updated field types to use selectize inputs for consistency.
* Made tips/warnings dismissible.
* Added 'Restore dismissed tips' utility to user preferences page.
* Converted `Site Info` global set to a single section.
* Added  `d` and `dd` twig functions that act like the core `dump` function and `dd` tag, but output the dump with better accessibility (opinionated, the standard output is not readable for old men like me in dark mode with small font size). 

## 2.1.0

Added Topics section as an example for taxonomies.

Still beta quality, but feature complete.

## 2.0.2 2023-02-18

Craft updates.

## 2.0.1 2023-02-16

Use sidebar layout for contact page.  This also fixes a layout issue.

## 2.0.0 2023-02-16

Beta quality.

This is the version we try to keep stable for plugin PoCs.

## 1.1.0 2023-01-05

* Ready for [Film Festival Plugin](https://github.com/wsydney76/craft-film-festival-light)
* Craft updates
* Various minor updates nobody remembers...