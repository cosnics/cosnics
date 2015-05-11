<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * $Id: reverter.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to revert a object from the users repository to a previous
 * state.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class ReverterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            $failures = 0;
            foreach ($ids as $object_id)
            {
                $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $object_id);
                if (! ($object->get_owner_id() == $this->get_user_id() || RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
                    RepositoryRights :: COLLABORATE_RIGHT,
                    $object->get_id(),
                    RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                    $object->get_owner_id())))
                {
                    throw new NotAllowedException();
                }

                if (\Chamilo\Core\Repository\Storage\DataManager :: content_object_revert_allowed($object))
                {
                    $object->version();
                }
                else
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                $message = Translation :: get(
                    'ObjectNotReverted',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get(
                    'ObjectReverted',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES);
            }
            $this->redirect(
                $message,
                ($failures ? true : false),
                array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS));
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

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS)),
                Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_reverter');
    }

    public function get_additional_parameters()
    {
        return parent :: get_additional_parameters(array(self :: PARAM_CONTENT_OBJECT_ID));
    }
}
