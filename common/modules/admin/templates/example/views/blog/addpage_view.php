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
            <form id="addForm" onsubmit="sendRequest('<?= cs_absolute_url('admin/admin-handler/add_page') ?>'); return false;">
                <label>
                    <input type="text" name="title" placeholder="title">
                </label><hr>
                <label>
                    <input type="text" name="author" placeholder="author">
                </label><hr>
                <label>
                    <input type="text" name="tag"  placeholder="tag">
                </label><hr>
                <label>
                    <textarea name="content" style="width: 100%; height: 500px;"></textarea>
                </label><hr>
                <button name="send">Add</button>
            </form>
        </article>
    </div>
</div>