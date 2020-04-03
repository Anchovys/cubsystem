<?php defined('CS__BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    function sendRequest(url) {

        var form = document.getElementById("addForm");
        var request = new XMLHttpRequest();

        request.open("POST", url);

        request.onreadystatechange = function()
        {
            if(this.readyState === 4 && this.status === 200)
            {
                alert(this.responseText);
            }
        };

        request.send(new FormData(form));
    }
</script>
<div class="mm__page">
    <div class="mm__page_title">
        <h1 class="mm__page_title_link mm__page_title_label">
            Добавьте новую категорию
        </h1>
    </div>
    <div class="mm__page_info">
    </div>
    <div class="mm__page_content">
        <article id="content">
            <form id="addForm" onsubmit="sendRequest('<?= csAbsoluteUrl('admin/admin-ajax/add_cat') ?>'); return false;">
                <label>
                    <input type="text" name="name" placeholder="Category name">
                </label><hr>
                <label>
                    <input type="text" name="link" placeholder="Category link">
                </label><hr>
                <label>
                    <textarea name="descr" rows="30" placeholder="Category description"></textarea>
                </label><hr>
                <button name="send">Add</button>
            </form>
        </article>
    </div>
</div>