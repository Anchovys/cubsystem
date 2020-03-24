<?php if(!defined('CS__BASEPATH') || !defined('CS__CFG')) die('Wrong path');

global $CS;

$content = '';
$segments = cs_get_segments();

if(!$_blog = $CS->gc('blog_module', 'modules'))
    die('Cannot load module / blog');

if(!$_tmpl = $CS->gc('template_helper', 'helpers'))
    die('Cannot load module / template');

switch($segments[0])
{
    case 'page': // first segment = page
        $page = $_blog->get_pages_by("link", $page_tag = $segments[1], 'full-page_view');
        $content .= (!$page) ? $_blog->page404('short-page_view') : $page;
    break;

    case 'tag': // first segment = tag
        $page = $_blog->get_pages_by("tag", $page_tag = $segments[1], 'short-page_view');
        $content .= (!$page) ? $_blog->page404('short-page_view') : $page;
    break;

    case '':
    case 'home': // first segment = home or empty
        $page = $_blog->get_pages_by(false, false, 'short-page_view');
        $content .= (!$page) ? $_blog->page404('short-page_view') : $page;
    break;

    default: // default segment (404)
        if(!$CS->gc('hooks_helper')->here('404-custom-hook')) // try load hooks
            $content .= $_blog->page404('short-page_view');
    break;
}

if($content == '')
    $content .= '<div class="blank">К сожалению, для вас ничего не найдено</div>';

if(file_exists($f = CS__TEMPLATE_VIEWS_DIR . "main_view.php")) // main template
    include_once($f);

?>