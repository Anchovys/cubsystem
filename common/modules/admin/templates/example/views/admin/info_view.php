<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');?>
<div class="mm__page">
    <div class="mm__page_content">
        <article id="content">
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 40%;">Version</td>
                        <td style="width: 60%;">{{$CS->info['name'] . ' rev.' . $CS->info['version']}}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%;">Template loaded</td>
                        <td style="width: 60%;"> - </td>
                    </tr>
                    <tr>
                        <td style="width: 40%;">Helpers loaded</td>
                        <td style="width: 60%;">{{count($CS->autoload['helpers'])}}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%;">Modules loaded</td>
                        <td style="width: 60%;">{{count($CS->autoload['modules'])}}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%;">Classes loaded</td>
                        <td style="width: 60%;">{{count($CS->autoload['classes'])}}</td>
                    </tr>
                </tbody>
            </table>
        </article>
    </div>
</div>