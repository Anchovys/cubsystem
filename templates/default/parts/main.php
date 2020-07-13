<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */ ?>
<div class="container h-100">
    <div class="row h-100">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="card">
                <div class="text-center">
                    <h1>{? $title ?}</h1>
                    <h2>{? $subtitle ?}</h2>
                    <small>Working time:
                        {{CsStats::getTimeInSeconds()}} sec.<br>
                        Memory usage: {{CsStats::getUsingMemoryString()}}
                    </small>
                </div>

                <!-- Выводим модули если есть -->
                {% if(isset($modules)) print '<hr>'; %}
                {? $modules ?}

            </div>
        </div>
        <div class="col-3"></div>
    </div>
</div>