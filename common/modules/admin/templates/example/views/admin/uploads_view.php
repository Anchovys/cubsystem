<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');?>
<script>
    function sendRequest(url, id) {

        var form = document.getElementById(id);
        var request = new XMLHttpRequest();

        request.open("POST", url);

        request.onreadystatechange = function()
        {
            if(this.readyState === 4 && this.status === 200)
            {
                alert(this.responseText);
            }
        };

        var data = new FormData(form);
        request.send(data);
    }
    </script>
<div class="mm__page">
    <div class="mm__page_content">
        <article id="content">
            <form id="addForm" onsubmit="sendRequest('<?= cs_abs_url('admin/admin-ajax/upload-file') ?>', 'addForm'); return false;">
                <input name="file" id="file-input" type="file" />
                <button name="send">Отправить</button>
            </form>
        </article>
    </div>
    <div class="mm__page_content">
        <article id="content">
            <?
            foreach (cs_in_path_files(CS_UPLOADSPATH) as $file)
            {
                ?>

                <div class="block" onclick="prompt('Путь до картинки', '<?=cs_path_to_url($file)?>')"><img src="<?= cs_path_to_url($file) ?>"></div>

                <?
            }
            ?>
        </article>
    </div>
</div>