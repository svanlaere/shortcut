<?php
if (!defined('IN_CMS')) {
    exit();
}

Plugin::setInfos(array(
    'id' => 'shortcut',
    'title' => 	__('Shortcut'),
    'description' => __('Include a snippet using a simple shortcut tag.'),
    'version' => '0.1.0',
    'license' => 'MIT',
    'author'  => 'svanlaere',
    'website' => 'http://svanlaere.nl/',
    'update_url' => 'http://svanlaere.nl/plugin-versions.xml',
    'require_wolf_version' => '0.7.0'
));

Observer::observe('page_found', 'sc_output');

function sc_returnsnippet($matches)
{
    $arg     = $matches[1];
    $snippet = Snippet::findByName(trim($arg));
    if (false == $snippet) {
        $arg = (int) $arg;
        if (is_int($arg)) {
            $snippet = Snippet::findById(trim($arg));
        } else
            return "<p>Snippet <strong>$arg</strong> not found!</p>" . PHP_EOL;
    }
    return $snippet->content_html;
}

function sc_output($page)
{
    $page_part = PagePart::findByPageId($page->id);
    if (count($page_part)) {
        foreach ($page_part as $part) {
            $part = $part->name;
            
            if (isset($page->part->$part->content_html) && !empty($page->part->$part->content_html) && $page->part->$part->content_html != '') {
                $body_before                     = $page->part->$part->content_html;
                $body_after                      = preg_replace_callback("'\[!(.*?)\!]'s", 'sc_returnsnippet', $body_before);
                $page->part->$part->content_html = $body_after;
            }
        }
    }
} 
