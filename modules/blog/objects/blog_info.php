<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

class cs_blog_info
{
    public $name = "Blog";
    public $description = "A simple blog realization.";

    function __construct(?array $data, ?array $needle = NULL)
    {
        if (isset($data['name']) && (!$needle || in_array('name', $needle)))
            $this->name = (string)$data['name'];

        if (isset($data['description']) && (!$needle || in_array('description', $needle)))
            $this->description = (string)$data['description'];
    }
}