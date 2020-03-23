<?php defined('CS__BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mm__page">
    <div class="mm__page_title">
        <a class="mm__page_title_link" href="page/<?=$__data['link']?>">
            <h1 class="mm__page_title_label"><?=$__data['title']?></h1>
        </a>
    </div>
    <div class="mm__page_info">
        <ul class="mm__page_info_list">
            <li>Автор:
                <?=$__data['author']?>
            </li>
            <li>Просмотров:
                <?=$__data['views']?>
            </li>
            <li>Комментариев:
                <?=$__data['comments']?>
            </li>
        </ul>
    </div>
    <div class="mm__page_content">
        <article id="content">
            <?=$__data['context']?>
        </article>
    </div>
</div>