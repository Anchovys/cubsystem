<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

global $CS;
$CS->template->setMeta(['script'=>'loginform_auth_AJAX.js']);
?>
<div class="mm__page">
    <div class="mm__page_title">
        <h1 class="mm__page_title_link mm__page_title_label">
            Авторизация
        </h1>
    </div>
    <div class="mm__page_info">
        Для продолжения, необходимо авторизоваться.

        {% if(isset($_GET['error'])): %}
        <p style="color: #ff7b82; border: 1px solid;">Ошибка при авторизации!</p>
        {% endif; %}
    </div>
    <div class="mm__page_content">
        <article id="content">
            <form id="loginForm" onsubmit="sendRequest('<?= csAbsoluteUrl('ajax/login') ?>'); return false;">
                <label for="login">
                    <input type="text" name="username" placeholder="Введите свой логин" value="admin">
                </label>
                <label for="password">
                    <input type="password" name="password" placeholder="Введите свой пароль" value="admin">
                </label>
                <label for="send_btn">
                    <button name="send_btn">Отправить</button>
                </label>
            </form>
        </article>
    </div>
</div>