<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class DeleterComponent extends Manager
{

    public function run()
    {
        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_REQUEST_ID);
        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = [$ids];
            }

            foreach ($ids as $id)
            {
                $request = DataManager::retrieve_by_id(Request::class, $id);

                if ($this->get_user()->isPlatformAdmin() ||
                    ($this->get_user_id() == $request->get_user_id() && $request->is_pending()))
                {
                    if (!$request->delete())
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
                    $parameter = ['OBJECT' => Translation::get('Request')];
                }
                elseif (count($ids) > $failures)
                {
                    $message = 'SomeObjectsNotDeleted';
                    $parameter = ['OBJECTS' => Translation::get('Requests')];
                }
                else
                {
                    $message = 'ObjectsNotDeleted';
                    $parameter = ['OBJECTS' => Translation::get('Requests')];
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeleted';
                    $parameter = ['OBJECT' => Translation::get('Request')];
                }
                else
                {
                    $message = 'ObjectsDeleted';
                    $parameter = ['OBJECTS' => Translation::get('Requests')];
                }
            }

            $this->redirectWithMessage(
                Translation::get($message, $parameter, StringUtilities::LIBRARIES), (bool) $failures,
                [Manager::PARAM_ACTION => Manager::ACTION_BROWSE]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', ['OBJECT' => Translation::get('Request')], StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }
}
