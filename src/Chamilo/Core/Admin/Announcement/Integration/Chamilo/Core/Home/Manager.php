<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array('Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home\Type\SystemAnnouncements');
    }
}