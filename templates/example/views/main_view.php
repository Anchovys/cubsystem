<?php defined('CS__BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    {{$__data->getBuffer('head')}}
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12"></div>
        </div>
    </div>
    <div class="container mm__main_container" style="margin-top:50px">
        <div class="row mm__main_row">
            <div class="col-md-12 col-lg-8 col-sm-12 mm__main_pagebar">
                {{$__data->getBuffer('body')}}
            </div>
            <div class="col-md-12 col-lg-4 col-sm-12 mm__main_sidebar">
                <div class="mm__sidebar">
                    <div class="mm__sidebar_content">
                        <div class="mm__widget">
                            <div class="mm__widget_header">
                                Меню
                            </div>
                            <div class="mm__widget_content">
                                <ul class="mm__vertical_menu">
                                    <li><a href="" class="mm_menu_item">Lorem ipsum dolor</a></li>
                                    <li><a href="" class="mm_menu_item">Lorem ipsum dolor</a></li>
                                    <li><a href="" class="mm_menu_item">Lorem ipsum dolor</a></li>
                                    <li><a href="" class="mm_menu_item">Lorem ipsum dolor</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="mm__footer">
                    Cubsystem cms {{$CS->info['version']}}, Copy, 2020
                    <br>
                    Page generated at: {{$CS->working_time()}}
                </div>
            </div>
        </div>
    </div>
</body>
</html>