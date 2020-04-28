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
            'openWith'  => '[cut]',
            'className' => 'cut_btn',
            'dropMenu'  =>
                [
                        [
                            'name'      => 'XCut',
                            'openWith'  => '[xcut]',
                            'className' => 'cut_btn',
                        ]
                ]
        ]
    ];

    function __construct()
    {
        $this->name = "Markitup Editor";
        $this->description = "A simple editor with BBcode support.";
        $this->version = "2";
        $this->fullpath = CS_MODULESCPATH . 'markitup_editor' . _DS;
        $this->config['autoload'] = TRUE;
    }


    // on load module
    public function onLoad()
    {
        global $CS;

        // make hook into render
        if ($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__admin-view', 'load', $this);
    }

    // on unload module
    public function onUnload()
    {

    }

    // on system install
    public function onInstall()
    {

    }

    // on enable that module
    public function onEnable()
    {

    }

    // on disable that module
    public function onDisable()
    {

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