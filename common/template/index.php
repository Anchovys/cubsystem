<?php if(!defined('CS__BASEPATH') || !defined('CS__CFG')) die('Wrong path');

global $CS;

define('CS__TEMPLATE_DIR',              CS__KERNELPATH    . 'template'    . _DS);
define('CS__TEMPLATE_ASSETS_DIR',       CS__TEMPLATE_DIR  . 'assets'      . _DS);
define('CS__TEMPLATE_COMPONENTS_DIR',   CS__TEMPLATE_DIR  . 'components'  . _DS);

$content = '';
$segments = cs_get_segments();

if(!$_blog = $CS->gc('blog_helper'))
    die('Cannot load module / blog');

switch($segments[0])
{
    case 'page':
        if($_blog->getCountsPageBy("link", $pageLink = $CS->dynamic['url-segments'][1]) > 0)
            $content .= $_blog->loadPageBy('link', $pageLink, CS__TEMPLATE_COMPONENTS_DIR . 'full-page.php');
        else $content .= $_blog->load404Page(CS__TEMPLATE_COMPONENTS_DIR . 'short-page.php');
    break;

    default:
        for($i = $_blog->getCountsPageBy(); $i >= 0; $i-- )
        {
            $content .= $_blog->loadPageBy('id', $i, CS__TEMPLATE_COMPONENTS_DIR . 'short-page.php');
        }
    break;
}

if(file_exists($f = CS__TEMPLATE_COMPONENTS_DIR . "main.php"))
    include_once($f);

?>