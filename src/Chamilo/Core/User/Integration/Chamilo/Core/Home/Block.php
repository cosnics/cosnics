<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Home;

class Block extends \Chamilo\Core\Home\BlockRendition
{

    public function is_visible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function get_folder()
    {
    }

    public function new_only()
    {
    }

    public function show_content()
    {
    }
}
