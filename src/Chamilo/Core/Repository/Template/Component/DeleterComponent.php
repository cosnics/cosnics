<?php
namespace Chamilo\Core\Repository\Template\Component;

use Chamilo\Core\Repository\Template\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * $Id: template_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $object_id)
            {
                $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $object_id);

                $versions = $object->get_content_object_versions();
                foreach ($versions as $version)
                {
                    $version->delete();
                }
            }

            if ($failures > 0)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get(
                        'ObjectNotDeleted',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get(
                        'ObjectsNotDeleted',
                        array('OBJECTS' => Translation :: get('ContentObjects')),
                        Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get(
                        'ObjectDeleted',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get(
                        'ObjectsDeleted',
                        array('OBJECTS' => Translation :: get('ContentObjects')),
                        Utilities :: COMMON_LIBRARIES);
                }
            }

            $this->redirect(
                $message,
                ($failures > 0),
                array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_TEMPLATES));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }
}
