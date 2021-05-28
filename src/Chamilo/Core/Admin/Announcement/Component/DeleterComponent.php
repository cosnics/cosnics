<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        $ids = $this->getRequest()->query->get(self::PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $this->set_parameter(self::PARAM_SYSTEM_ANNOUNCEMENT_ID, $ids);
        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $publication = $this->getPublicationService()->findPublicationByIdentifier((int) $id);

                if (!$this->getPublicationService()->deletePublication($publication))
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ContentObjectNotDeleted';
                    $parameter = array('OBJECT' => Translation::get('Publication'));
                }
                else
                {
                    $message = 'ContentObjectsNotDeleted';
                    $parameter = array('OBJECTS' => Translation::get('Publications'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ContentObjectDeleted';
                    $parameter = array('OBJECT' => Translation::get('Publication'));
                }
                else
                {
                    $message = 'ContentObjectsDeleted';
                    $parameter = array('OBJECTS' => Translation::get('Publications'));
                }
            }

            $this->redirect(
                Translation::get($message, $parameter, Utilities::COMMON_LIBRARIES), (bool) $failures,
                array(self::PARAM_ACTION => self::ACTION_BROWSE)
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', array('OBJECTS' => Translation::get('Publication')),
                        Utilities::COMMON_LIBRARIES
                    )
                )
            );
        }
    }
}
