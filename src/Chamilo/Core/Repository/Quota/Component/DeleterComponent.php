<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\Repository\Quota\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DeleterComponent extends Manager
{

    public function run()
    {
        $ids = $this->getRequest()->get(self :: PARAM_REQUEST_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $request = DataManager :: retrieve_by_id(Request :: class_name(), (int) $id);

                if (/*(\core\repository\quota\rights\Rights :: get_instance()->quota_is_allowed() && \core\repository\quota\rights\Rights :: get_instance()->is_target_user(
                        $this->get_user(), $request->get_user_id())) || */$this->get_user()->is_platform_admin() ||
                     ($this->get_user_id() == $request->get_user_id() && $request->is_pending()))
                {
                    if (! $request->delete())
                    {
                        $failures ++;
                    }
                }
                else
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotDeleted';
                    $parameter = array('OBJECT' => Translation :: get('Request'));
                }
                elseif (count($ids) > $failures)
                {
                    $message = 'SomeObjectsNotDeleted';
                    $parameter = array('OBJECTS' => Translation :: get('Requests'));
                }
                else
                {
                    $message = 'ObjectsNotDeleted';
                    $parameter = array('OBJECTS' => Translation :: get('Requests'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeleted';
                    $parameter = array('OBJECT' => Translation :: get('Request'));
                }
                else
                {
                    $message = 'ObjectsDeleted';
                    $parameter = array('OBJECTS' => Translation :: get('Requests'));
                }
            }

            $this->redirect(
                Translation :: get($message, $parameter, Utilities :: COMMON_LIBRARIES),
                ($failures ? true : false),
                array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('Request')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }
}
