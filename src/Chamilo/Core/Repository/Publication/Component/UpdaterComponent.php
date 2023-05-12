<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to update an object publication.
 */
class UpdaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $application = Request::get(self::PARAM_PUBLICATION_APPLICATION);
        $publication_id = Request::get(self::PARAM_PUBLICATION_ID);
        $publicationContext = $this->getRequest()->getFromRequestOrQuery(self::PARAM_PUBLICATION_CONTEXT);

        $this->set_parameter(self::PARAM_PUBLICATION_ID, $publication_id);
        $this->set_parameter(self::PARAM_PUBLICATION_APPLICATION, $application);
        $this->set_parameter(self::PARAM_PUBLICATION_CONTEXT, $publicationContext);

        if (! empty($application) && ! empty($publication_id))
        {
            $publication_attributes = DataManager::get_content_object_publication_attribute(
                $publication_id,
                $application,
                $publicationContext
            );
            $latest_version = $publication_attributes->get_content_object()->get_latest_version_id();

            $publication_attributes->set_content_object_id($latest_version);
            $success = $publication_attributes->update();

            $this->redirectWithMessage(
                Translation::get(
                    $success ? 'ObjectUpdated' : 'ObjectNotUpdated',
                    array('OBJECT' => Translation::get('Publication')),
                    StringUtilities::LIBRARIES), !$success,
                array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            $this->display_warning_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation::get('Publication')),
                        StringUtilities::LIBRARIES)));
        }
    }
}
