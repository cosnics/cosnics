<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;

class PublicationChange extends ChangesTracker
{
    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_weblcms_publication_change';
    }
}
