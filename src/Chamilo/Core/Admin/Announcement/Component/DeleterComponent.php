<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Libraries\Utilities\StringUtilities;

class DeleterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

        $translator = $this->getTranslator();

        $ids = $this->getRequest()->query->get(self::PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $this->set_parameter(self::PARAM_SYSTEM_ANNOUNCEMENT_ID, $ids);
        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = [$ids];
            }

            foreach ($ids as $id)
            {
                $publication = $this->getPublicationService()->findPublicationByIdentifier($id);

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
                    $parameter = ['OBJECT' => $translator->trans('Publication', [], Manager::CONTEXT)];
                }
                else
                {
                    $message = 'ContentObjectsNotDeleted';
                    $parameter = ['OBJECTS' => $translator->trans('Publications', [], Manager::CONTEXT)];
                }
            }
            elseif (count($ids) == 1)
            {
                $message = 'ContentObjectDeleted';
                $parameter = ['OBJECT' => $translator->trans('Publication', [], Manager::CONTEXT)];
            }
            else
            {
                $message = 'ContentObjectsDeleted';
                $parameter = ['OBJECTS' => $translator->trans('Publications', [], Manager::CONTEXT)];
            }

            $this->redirectWithMessage(
                $translator->trans($message, $parameter, StringUtilities::LIBRARIES), (bool) $failures,
                [self::PARAM_ACTION => self::ACTION_BROWSE]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['OBJECTS' => $translator->trans('Publication')], StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }
}
