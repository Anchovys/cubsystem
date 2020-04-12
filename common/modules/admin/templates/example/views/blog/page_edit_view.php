<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

// по аргументу передается страница (если есть)
$page = $__data['page'];

?>
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

        request.send(new FormData(form));
    }

</script>
<div class="mm__page">
    <div class="mm__page_title">
        <h1 class="mm__page_title_link mm__page_title_label">
            <?= !isset($page) ? 'Добавление новой' : 'Редактирование' ?> записи
        </h1>
    </div>
    <div class="mm__page_info">
    </div>
    <div class="mm__page_content">
        <article id="content">
            <form id="addForm" onsubmit="sendRequest('<?= cs_abs_url('admin/admin-ajax/page_edit') ?>', 'addForm'); return false;">
                <input type="hidden" name="page-id" value="<?=isset($page) ? ($page->id) : '0';?> ">
                <label>
                    <input type="text" name="title" required placeholder="Article title" value="<?=isset($page)?$page->title:'';?>">
                </label><hr>
                <label>
                    <textarea name="content" required id="editor" rows="30" placeholder="Page text (html, BBcode support)"><?

                        if(isset($page))
                        {
                            // dont cut
                            if($page->cut_type === 0)
                                print $page->short_text . $page->full_text;
                            else
                            {
                                // cut other types
                                print $page->short_text;
                                print $page->cut_type === 1 ? '[xcut]' : '[cut]';
                                print $page->full_text;
                            }
                        }
                        ?></textarea>
                </label><hr>
                <label>
                    <?
                    $cats = cs_cat::getListAll(FALSE, ['name', 'id'], FALSE);
                        if($cats !== NULL && $cats['count'] !== 0)
                            foreach ($cats['result'] as $cat)
                            {
                                $checked = (isset($page) and in_array($cat->id, $page->cat_ids)) ? 'checked=""' : '';
                                print "<input type='checkbox' id='cat_{$cat->id}' name='category[]' value='{$cat->id}' style='width: auto;' {$checked}>";
                                print "<label for='cat_{$cat->id}'>{$cat->name}</label>";
                            }
                        else print 'нет ни одной категории.';
                            ?>
                </label><hr>
                <label>
                    <input type="text" name="author-id" placeholder="Article author" value="<?= isset($page) ? ($page->author_id) : ''; ?>">
                </label><hr>
                <label>
                    <input type="text" name="link" placeholder="Article link" value="<?= isset($page) ? ($page->link) : ''; ?>">
                </label><hr>
                <label>
                    <input type="text" required name="tag" placeholder="Article tag" value="<?= isset($page) ? ($page->tag) : ''; ?>">
                </label><hr>

                <label>
                    <input type="text" name="meta_title" placeholder="Meta title" value="<?= isset($page) ? ($page->meta['title']) : ''; ?>">
                </label><hr>

                <label>
                    <textarea name="meta_description" rows="10" placeholder="Meta description"><?= isset($page) ? ($page->meta['desc']) : ''; ?></textarea>
                </label><hr>

                <button name="send"><?= !isset($page) ? 'Добавить' : 'Отредактировать' ?> </button>
            </form>
            <? if(isset($page)):?>
            <form id="removeForm"  onsubmit="sendRequest('<?= cs_abs_url('admin/admin-ajax/page_del') ?>', 'removeForm'); return false;">
                <input type="hidden" name="page-id" value="<?=isset($page) ? ($page->id) : '0';?> ">
                <button name="send">Удалить страницу</button>
            </form>
            <? ENDIF; ?>
        </article>
    </div>
</div>