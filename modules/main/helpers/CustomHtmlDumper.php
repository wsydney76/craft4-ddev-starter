<?php

namespace modules\main\helpers;


use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class CustomHtmlDumper extends HtmlDumper
{
    protected static $themes = [
        'light' => [
            'default' => 'background:none; color:#CC7832; line-height:1.2em; font:17px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
            'num' => 'font-weight:bold; color:#2563eb',
            'const' => 'font-weight:bold; color:#b45309',
            'str' => 'font-weight:bold; color:#15803d;',
            'note' => 'color:#1E40AF',
            'ref' => 'color:#6E6E6E',
            'public' => 'color:#262626',
            'protected' => 'color:#262626',
            'private' => 'color:#262626',
            'meta' => 'color:#B729D9',
            'key' => 'color:#78350F',
            'index' => 'color:#2563EB',
            'ellipsis' => 'color:#CC7832',
            'ns' => 'user-select:none;',
        ],
    ];
}