<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request::get(self::PARAM_PUBLICATION_ID);
        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $publication = DataManager::retrieve_by_id(Publication::class, $id);

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
                    $message = Translation::get(
                        'ObjectNotDeleted', array('OBJECT' => Translation::get('Publication')),
                        StringUtilities::LIBRARIES
                    );
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsNotDeleted', array('OBJECT' => Translation::get('Publications')),
                        StringUtilities::LIBRARIES
                    );
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectDeleted', array('OBJECT' => Translation::get('Publication')), StringUtilities::LIBRARIES
                    );
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsDeleted', array('OBJECT' => Translation::get('Publications')),
                        StringUtilities::LIBRARIES
                    );
                }
            }

            $this->redirectWithMessage(
                $message, (bool) $failures, array(
                    \Chamilo\Application\Calendar\Manager::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::context(
                    ),
                    \Chamilo\Application\Calendar\Manager::PARAM_ACTION => \Chamilo\Application\Calendar\Manager::ACTION_BROWSE
                )
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectsSelected', null, StringUtilities::LIBRARIES))
            );
        }
    }
}
