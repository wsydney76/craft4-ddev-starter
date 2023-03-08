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

$cpTrigger = 'admin';
$isCpRequest = str_starts_with($_SERVER['REQUEST_URI'], "/$cpTrigger") || str_starts_with($_GET['p'], $cpTrigger);

return
	GeneralConfig::create()
		->devMode($isDev)
		->allowAdminChanges($isDev)
        ->cpTrigger($cpTrigger)

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
        ->translationDebugOutput(false)

		->defaultTemplateExtensions(['twig'])
		->enableTemplateCaching($isProd)

		->convertFilenamesToAscii(true)
		->maxUploadFileSize('32M')
		->generateTransformsBeforePageLoad(!$isCpRequest)
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

