# Changelog

## Unreleased

* Update to Craft 4.4.5
* Update to Tailwind 3.3.0
* Added `Align` setting to `gallery` block.
* Added `Custom` purifier config to `text` block.
* Added `redactor` and `htmlpurifier` configuration files from `craft/craft` repo.
* Streamlined main/init.
* Added bigger image columns to element index
* Added `Story` entry type for the `News` section.
* Improved display of copyright notices in footer.
* Added `requestData` app component and `request` twig variable, representing a Laravel collection that can be used to simulate a request scope (experimental).
* Prepared for rtl languages (needs review).
* Dropped environment variable fields from `Site Info` single entry field layout, as they do not support drafts. The fields are still available if you want to use them in a global set. 


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