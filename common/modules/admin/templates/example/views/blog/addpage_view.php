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
            Добавьте новую запись
        </h1>
    </div>
    <div class="mm__page_info">
    </div>
    <div class="mm__page_content">
        <article id="content">
            <form id="addForm" onsubmit="sendRequest('<?= cs_abs_url('admin/admin-ajax/add_page') ?>'); return false;">
                <label>
                    <input type="text" name="title" placeholder="Article title">
                </label><hr>
                <label>
                    <input type="text" name="author-id" placeholder="Article author">
                </label><hr>
                <label>
                    <input type="text" name="link" placeholder="Article link">
                </label><hr>
                <label>
                    <input type="text" name="tag" placeholder="Article tag">
                </label><hr>
                <label>
                    {% $cats = cs_cat::getListAll(FALSE, ['name', 'id'], FALSE);
                    if($cats['count'] != 0)
                    foreach ($cats['result'] as $cat)
                        print "<input type=\"checkbox\" id=\"cat_$cat->id\" name=\"cat[]\" value=\"$cat->id\" style='width: auto;'><label for=\"cat$cat->id\">$cat->name</label>"; %}
                </label><hr>
                <label>
                    <textarea name="content" rows="30" placeholder="Page text (html, BBcode support)"></textarea>
                </label><hr>
                <button name="send">Add</button>
            </form>
        </article>
    </div>
</div>