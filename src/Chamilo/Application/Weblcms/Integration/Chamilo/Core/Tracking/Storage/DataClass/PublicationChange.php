<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;

class PublicationChange extends ChangesTracker
{
    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'tracking_weblcms_publication_change';
    }
}
