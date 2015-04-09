<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectRemover;

class Remover extends ContentObjectRemover
{

    public function get_additional_packages()
    {
        $installers = array();
        $installers[] = 'core\repository\content_object\rss_feed\integration\core\home';
        return $installers;
    }
}
