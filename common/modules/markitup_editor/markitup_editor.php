<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
class markitup_editor_module extends cs_module
{
    public $options = [
        [
            'name'      => 'Bold',
            'key'       => 'B',
            'openWith'  => '[b]',
            'closeWith' => '[/b]',
            'className' => 'bold_btn'
        ],
        [
            'name'      => 'Cut',
            'key'       => 'C',
            'openWith'  => '[x]',
            'className' => 'cut_btn'
        ]
    ];
    function __construct()
    {
        global $CS;
        $this->name = "Markitup Editor";
        $this->description = "A simple editor with BBcode support.";
        $this->version = "1";
        $this->fullpath = CS_MODULESCPATH . 'markitup_editor' . _DS;

        // make hook into render
        if ($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__admin-view', 'load', $this);
    }

    function load()
    {
        global $CS;
        $path = cs_path_to_url($this->fullpath . 'markitup' . _DS);
        ?>

        <script type="text/javascript" src="<?=$path?>/jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="<?=$path?>/jquery.markitup.js"></script>

        <link rel="stylesheet" type="text/css" href="<?=$path?>/sets.css">
        <link rel="stylesheet" type="text/css" href="<?=$path?>/skins/simple/style.css">

        <script type="text/javascript">
            $(function() {
                $('#editor').markItUp({
                    onShiftEnter: {keepDefault: false, replaceWith: '<br />\n'},
                    onCtrlEnter: {keepDefault: false, openWith: '\n<p>', closeWith: '</p>'},
                    onTab: {keepDefault: false, replaceWith: '    '},
                    markupSet:  <?=json_encode($this->options)?>
                });
            });
        </script>

        <?php
    }
}
?>