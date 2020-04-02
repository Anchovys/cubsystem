<?php defined('CS__BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mm__page mm__page_full">
    <div class="mm__page_title">
        <h1 class="mm__page_title_link mm__page_title_label">
            {{$__data['page']->title}}
        </h1>
    </div>
    <div class="mm__page_content">
        <article id="content">
            {{$__data['page']->context}}
        </article>
    </div>
    <div class="mm__page_info">
        <ul class="mm__page_info_list">
            <li>Автор: {{$__data['page']->author}}</li>
            <li>Просмотров: {{$__data['page']->views}}</li>
            <li>Комментариев: {{$__data['page']->comments}}</li>
        </ul>
    </div>
    <div class="mm__other_pages">
        <h3>
            Other pages
        </h3>
    </div>
    <div class="mm__comments">
        <h2>
            Comments
        </h2>        
    </div>
</div>