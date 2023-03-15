<?php

namespace modules\main\helpers;


use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * Improves accessiblity of Symfony HtmlDumper, increases font-size and color contrast
 */
class CustomHtmlDumper extends HtmlDumper
{
    protected static $themes = [
        'light' => [
            'default' => 'background:white; color:#78350f; line-height:1.2em; font:17px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
            'num' => 'font-weight:bold; color:#1e40af',
            'const' => 'font-weight:bold; color:#92400e',
            'str' => 'font-weight:bold; color:#166534;',
            'note' => 'color:#1E40AF',
            'ref' => 'color:#404040',
            'public' => 'color:#262626',
            'protected' => 'color:#262626',
            'private' => 'color:#262626',
            'meta' => 'color:#86198f',
            'key' => 'color:#78350F',
            'index' => 'color:#1e40af',
            'ellipsis' => 'color:#78350f',
            'ns' => 'user-select:none;',
        ],
    ];
}