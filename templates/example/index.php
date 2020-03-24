<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| index.php, Назначение: входной файл шаблона
| -------------------------------------------------------------------------
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

global $CS;

$segments = cs_get_segments();

if(!$_blog = $CS->gc('blog_module', 'modules'))
    die('Cannot load module / blog');

$_blog->template = $this;

switch($segments[0])
{
    case 'page': // first segment = page
        $page = $_blog->get_pages_by("link", $page_tag = $segments[1], 'full-page_view');
        $this->html_buffer .= (!$page) ? $_blog->page404('short-page_view') : $page;
    break;

    case 'tag': // first segment = tag
        $page = $_blog->get_pages_by("tag", $page_tag = $segments[1], 'short-page_view', FALSE);
        $this->html_buffer .= (!$page) ? $_blog->page404('short-page_view') : $page;
        $this->meta_data = [
            'title' => "Tag: {$page_tag}",
            'description' => "Here you can see all page with tag: {$page_tag}"
        ];
    break;

    case '':
    case 'home': // first segment = home or empty
        $page = $_blog->get_pages_by(false, false, 'short-page_view', FALSE);
        $this->html_buffer .= (!$page) ? $_blog->page404('short-page_view') : $page;
        $this->meta_data = [
            'title' => "Home Page",
            'description' => "Welcome to our home page!"
        ];
    break;

    default: // default segment (404)
        if(!$CS->gc('hooks_helper')->here('404-custom-hook')) // try load hooks
            $this->html_buffer .= $_blog->page404('short-page_view');
    break;
}

if($this->html_buffer == '')
    $this->html_buffer .= '<div class="blank">No content</div>';

$data =
    [
        'content'=> $this->html_buffer,
        'meta' => $this->generate_meta($this->meta_data)
    ];

$this->html_buffer = $this->callback_load($data , 'main_view');

if($this->settings['minify-html'] && $minify = $CS->gc('html_minify_helper', 'helpers'))
    $this->html_buffer = $minify->minify($this->html_buffer);

?>