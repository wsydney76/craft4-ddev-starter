<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see \craft\config\GeneralConfig
 */

use craft\config\GeneralConfig;
use craft\helpers\App;

$isDev = App::env('CRAFT_ENVIRONMENT') === 'dev';
$isProd = App::env('CRAFT_ENVIRONMENT') === 'production';

return
	GeneralConfig::create()
		->devMode($isDev)
		->allowAdminChanges($isDev)

		->maxRevisions(10)

		->omitScriptNameInUrls(true)
        ->cpHeadTags([
            ['link', ['rel' => 'icon', 'href' => '/favicon.ico']],
        ])
		->limitAutoSlugsToAscii(true)

		->preventUserEnumeration(true)
		->sendPoweredByHeader(false)
		->disallowRobots(!$isProd)
        ->errorTemplatePrefix('_errors/')

		->defaultTemplateExtensions(['twig'])
		->enableTemplateCaching($isProd)

		->convertFilenamesToAscii(true)
		->maxUploadFileSize('32M')
		->generateTransformsBeforePageLoad(true)
		->optimizeImageFilesize(false)
		->revAssetUrls(true)

		->useIframeResizer(true)

        ->enableGql(false)

        ->extraNameSalutations(['Professor', 'Doktor'])
        ->extraLastNamePrefixes(['zu', 'und'])

		->aliases([
			// Prevent the @web alias from being set automatically (cache poisoning vulnerability)
			'@web' => App::env('PRIMARY_SITE_URL'),
			// Lets `./craft clear-caches all` clear CP resources cache
			'@webroot' => dirname(__DIR__) . '/web',
		]);

