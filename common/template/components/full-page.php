<?php defined('CS__BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mm__page mm__page_full">
    <div class="mm__page_title">
        <h1 class="mm__page_title_link mm__page_title_label"><?=$__data['title']?></h1>
    </div>
    <div class="mm__page_content">
        <article id="content">
            <?=$__data['context']?>
        </article>
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
            <!--
            <li>
                <div class="elem rater_block" style="">
                    <div id="rater" style="float:left;" title="Текущая оценка: 10. Голосов: 3">
                        <ul class="star-rating" style="width:250px">
                            <li class="current-rating" style="width:90%;">9/10</li>
                            <li class="star"><a href="#1" title="1/10" style="width:10%;z-index:11">1</a></li>
                            <li class="star"><a href="#2" title="2/10" style="width:20%;z-index:10">2</a></li>
                            <li class="star"><a href="#3" title="3/10" style="width:30%;z-index:9">3</a></li>
                            <li class="star"><a href="#4" title="4/10" style="width:40%;z-index:8">4</a></li>
                            <li class="star"><a href="#5" title="5/10" style="width:50%;z-index:7">5</a></li>
                            <li class="star"><a href="#6" title="6/10" style="width:60%;z-index:6">6</a></li>
                            <li class="star"><a href="#7" title="7/10" style="width:70%;z-index:5">7</a></li>
                            <li class="star"><a href="#8" title="8/10" style="width:80%;z-index:4">8</a></li>
                            <li class="star"><a href="#9" title="9/10" style="width:90%;z-index:3">9</a></li>
                            <li class="star"><a href="#10" title="10/10" style="width:100%;z-index:2">10</a></li>
                        </ul>
                    </div>
                </div>
            </li>
            -->
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