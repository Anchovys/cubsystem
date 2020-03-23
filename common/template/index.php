<?php if(!defined('CS__BASEPATH') || !defined('CS__CFG')) die('Wrong path');

global $CS;

define('CS__TEMPLATE_DIR',              CS__KERNELPATH    . 'template'    . _DS);
define('CS__TEMPLATE_ASSETS_DIR',       CS__TEMPLATE_DIR  . 'assets'      . _DS);
define('CS__TEMPLATE_VIEWS_DIR',        CS__TEMPLATE_DIR  . 'views'       . _DS);

$content = '';
$segments = cs_get_segments();

if(!$_blog = $CS->gc('blog_helper'))
    die('Cannot load module / blog');

switch($segments[0])
{
    case 'page': // first segment = page
        if($_blog->getPagesBy("link", $page_link = $CS->dynamic['url-segments'][1], true) > 0)
            $content .= $_blog->loadPageBy('link', $page_link, CS__TEMPLATE_VIEWS_DIR . 'full-page_view.php');
        else $content .= $_blog->load404Page(CS__TEMPLATE_VIEWS_DIR . 'short-page_view.php');
    break;

    case 'tag': // first segment = tag
        foreach($_blog->getPagesBy("tag", $page_tag = $CS->dynamic['url-segments'][1], false) as $page_data)
            $content .= $_blog->loadPageBy('id', $page_data['id'], CS__TEMPLATE_VIEWS_DIR . 'short-page_view.php');
    break;

    case '':
    case 'home': // first segment = home or empty
        for($id = $_blog->getPagesBy(false, false, true); $id >= 0; $id-- )
            $content .= $_blog->loadPageBy('id', $id, CS__TEMPLATE_VIEWS_DIR . 'short-page_view.php');
    break;

    default: // default segment (404)
        if(!$CS->gc('hooks_helper')->here('404-custom-hook')) // try load hooks
            $content .= $_blog->load404Page(CS__TEMPLATE_VIEWS_DIR . 'short-page_view.php');
    break;
}

if($content == '')
    $content .= '<div class="blank">К сожалению, для вас ничего не найдено</div>';

if(file_exists($f = CS__TEMPLATE_VIEWS_DIR . "main_view.php")) // main template
    include_once($f);

?>