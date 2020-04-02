<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');?>
<div class="mm__page">
    <div class="mm__page_title">
        <h1 class=" mm__page_title_link mm__page_title_label">
            {{$__data['page']->title}}
        </h1>
    </div>
    <div class="mm__page_info">

    </div>
    <div class="mm__page_content">
        <article id="content">
            {{$__data['page']->context}}
        </article>
    </div>
</div>