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
                    <h1><?= $title; ?></h1>
                    <h2><?= $subtitle; ?></h2>
                    <small>Working time:
                        <?=round(microtime(TRUE) - $CS->info->getOption('start_time'), 3)?>
                        sec.
                    </small>
                </div>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
</div>