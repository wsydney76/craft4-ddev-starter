# Documentation for Media Cookies Preference Alpine.js Store

## Overview

This Alpine.js store module manages a user's preference regarding media cookies. It encapsulates all necessary functionality for setting, getting, and checking the user's preference.

The following methods and properties are available in this store:

## Properties

### `_mediaCookiesAllowed`

A private property that holds the value of the user's preference for allowing media cookies.

## Methods

### `init()`

This method initializes the store by reading the `mediaCookiesAllowed` cookie, if it exists, and storing its value in the `_mediaCookiesAllowed` property. It should be called once during the initialization of the Alpine.js component.

Will be called automatically when the store is initialized.

### `get mediaCookiesAllowed()`

This getter method returns the user's preference for allowing media cookies. If the user has not made a choice yet (i.e., if `_mediaCookiesAllowed` is `undefined`), it assumes that consent has not been given and returns `false`. Otherwise, it returns `true` if `_mediaCookiesAllowed` is `'allowed'`, and `false` otherwise.

### `allowMediaCookies()`

This method sets the user's preference for allowing media cookies to `'allowed'`. It updates both the `_mediaCookiesAllowed` property and the `mediaCookiesAllowed` cookie. The cookie is set to expire in 365 days.

### `rejectMediaCookies()`

This method sets the user's preference for allowing media cookies to `'rejected'`. It updates both the `_mediaCookiesAllowed` property and the `mediaCookiesAllowed` cookie. The cookie is set to expire in 365 days.

### `get promptForUserChoice()`

This getter method returns `true` if the user has not yet made a choice about media cookies (i.e., if `_mediaCookiesAllowed` is `undefined`), and `false` otherwise. This can be used to determine if the user should be prompted to make a choice about allowing media cookies.

## Usage

You can use this store in your Alpine.js components to manage the user's media cookie preference. Based on the return value of `promptForUserChoice`, prompt the user to make a choice about media cookies, and call `allowMediaCookies()` or `rejectMediaCookies()` based on their choice. Use `mediaCookiesAllowed` to check the user's current preference.

### Init in your bundle

```javascript
import Consent from './stores/Consent';
Alpine.store('consent', Consent)
```

### Use methods in your Alpine.js component

```html

<div x-show="$store.consent.promptForUserChoice"></div>
<div x-if="$store.consent.mediaCookiesAllowed"></div>

<div @click="$store.consent.allowMediaCookies()"></div>
<div @click="$store.consent.rejectMediaCookies()"></div>
```
**Note:** This store assumes the presence of a `Cookies` object with `get` and `set` methods for reading and writing cookies. This could be a reference to the [js-cookie](https://github.com/js-cookie/js-cookie) library or any similar library or implementation.