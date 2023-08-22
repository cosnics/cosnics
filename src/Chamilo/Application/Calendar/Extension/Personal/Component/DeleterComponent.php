<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $ids = $this->getRequest()->query->get(self::PARAM_PUBLICATION_ID);
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

                if (!$this->getRightsService()->isAllowedToDeletePublication($publication, $this->getUser()))
                {
                    throw new NotAllowedException();
                }

                if (!$this->getPublicationService()->deletePublication($publication))
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = $translator->trans(
                        'ObjectNotDeleted', ['OBJECT' => $translator->trans('Publication', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
                else
                {
                    $message = $translator->trans(
                        'ObjectsNotDeleted', ['OBJECT' => $translator->trans('Publications', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
            }
            elseif (count($ids) == 1)
            {
                $message = $translator->trans(
                    'ObjectDeleted', ['OBJECT' => $translator->trans('Publication', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }
            else
            {
                $message = $translator->trans(
                    'ObjectsDeleted', ['OBJECT' => $translator->trans('Publications', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }

            $this->redirectWithMessage(
                $message, (bool) $failures, [
                    Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Application\Calendar\Manager::ACTION_BROWSE
                ]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities($translator->trans('NoObjectsSelected', [], StringUtilities::LIBRARIES))
            );
        }
    }
}
