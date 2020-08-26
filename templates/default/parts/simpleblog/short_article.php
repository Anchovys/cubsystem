<?php defined('CS__BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="blog-post">
    <a href="{{ CsUrl::absUrl('article/' . $slug) }}">
        <h3 class="blog-post-title">{? $title ?}</h3>
    </a>
    <p class="blog-post-meta">December 14, 2013 by <a href="#">Chris</a></p>
    <p>
        {? $text ?}
    </p>
</div>