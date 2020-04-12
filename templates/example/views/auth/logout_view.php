<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

global $CS;
$CS->template->setMeta(['script'=>'loginform_auth_AJAX.js']);

?>
<div class="mm__page">
    <div class="mm__page_title">
        <h1 class="mm__page_title_link mm__page_title_label">
            Вы уже авторизованы
        </h1>
    </div>
    <div class="mm__page_info">
        {{ $__data->name }}, Вы хотите выйти?
    </div>
    <div class="mm__page_content">
        <article id="content">
            <form id="logoutForm" onsubmit="sendRequest('<?= cs_abs_url('ajax/logout') ?>', 'logoutForm'); return false;">
                <label for="send_btn">
                    <button name="send_btn">Да, выйти</button>
                </label>
            </form>
        </article>
    </div>
</div>