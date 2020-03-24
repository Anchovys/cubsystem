<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');?>
<div class="mm__page">
    <div class="mm__page_title">
        <a class="mm__page_title_link" href="page/<?=$__data['page']->link?>">
            <h1 class="mm__page_title_label">
                <?=$__data['page']->title?>
            </h1>
        </a>
    </div>
    <div class="mm__page_info">
        <ul class="mm__page_info_list">
            <li>Автор:
                <?=$__data['page']->author?>
            </li>
            <li>Просмотров:
                <?=$__data['page']->views?>
            </li>
            <li>Комментариев:
                <?=$__data['page']->comments?>
            </li>
        </ul>
    </div>
    <div class="mm__page_content">
        <article id="content">
            <?=$__data['page']->context?>
        </article>
    </div>
</div>