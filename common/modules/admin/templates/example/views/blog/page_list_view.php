<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');?>
<div class="mm__page">
    <div class="mm__page_title">
        <a class="mm__page_title_link" href="{{cs_abs_url('page/' . $__data['page']->link)}}">
            <h1 class="mm__page_title_label">
                Просмотр всех записей
            </h1>
        </a>
    </div>
    <div class="mm__page_info">

    </div>
    <div class="mm__page_content">
        <article id="content">
            <table style="width: 100%;">
                <tbody>
                <tr>
                    <td style="width: 40%;">ID</td>
                    <td style="width: 60%;">TITLE</td>
                </tr>

                <?
                    $pages = cs_page::getListAll();
                    if($pages['count'] === 0) print 'nothing to show';
                    else foreach ($pages['result'] as $page)
                        print("<tr>
                            <td><a href='".cs_abs_url('admin/page_edit/'). $page->id ."'>{$page->id}</a></td>
                            <td><a href='".cs_abs_url('page/'). $page->link ."'>{$page->title}</a></td>
                        </tr>");
                ?>
                </tbody>
            </table>
        </article>
    </div>
</div>