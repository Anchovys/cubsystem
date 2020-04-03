<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');?>
<div class="mm__page">
    <div class="mm__page_title">
        <a class="mm__page_title_link" href="{{cs_abs_url('page/' . $__data['page']->link)}}">
            <h1 class="mm__page_title_label">
                {{$__data['page']->title}}
            </h1>
        </a>
    </div>
    <div class="mm__page_info">
        <ul class="mm__page_info_list">
            <li>Автор: {{$__data['page']->author}}</li>
            <li>Просмотров: {{$__data['page']->views}}</li>
            <li>Комментариев: {{$__data['page']->comments}}</li>
        </ul>
        <ul class="mm__page_info_list">
            <li>Категория: {% if($__data['page']->cat === NULL) print 'нет'; else foreach($__data['page']->cat as $cat)
                print ('<a href="'.cs_abs_url('cat/' . $cat->id).'">' . $cat->name . '</a> '); %}
             </li>
        </ul>
    </div>
    <div class="mm__page_content">
        <article id="content">
            {{$__data['page']->context}}
        </article>
    </div>
</div>