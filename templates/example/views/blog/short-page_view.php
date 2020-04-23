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
            <li>Автор: {% if($__data['page']->author) print $__data['page']->author->name; else print 'нет'; %}</li>
            <li>Просмотров: {{$__data['page']->views}}</li>
            <li>Комментариев: {{$__data['page']->comments}}</li>
            <li><time datetime="{{$__data['page']->date}}">Опубликовано: {{$__data['page']->date}}</time></li>
        </ul>
        <ul class="mm__page_info_list">
            <li>Категория: {% if($__data['page']->cats) foreach($__data['page']->cats as $cat)
                print ('<a href="'.cs_abs_url('category/' . $cat->link , '/').'">' . $cat->name . '</a> '); else print 'нет'; %}
             </li>
        </ul>
    </div>
    <div class="mm__page_content">
        <article id="content">
            {{$__data['page']->short_text}}
        </article>
    </div>
</div>