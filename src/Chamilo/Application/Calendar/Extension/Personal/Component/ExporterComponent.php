<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ExporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $objectTranslation = Translation::getInstance()->getTranslation(
            'PersonalCalendarPublication', null, Manager::context()
        );

        $id = Request::get(self::PARAM_PUBLICATION_ID);
        if (!$id)
        {
            throw new NoObjectSelectedException($objectTranslation);
        }

        $calendar_event_publication = DataManager::retrieve_by_id(Publication::class_name(), $id);

        if (!$calendar_event_publication instanceof Publication)
        {
            throw new ObjectNotExistException($objectTranslation, $id);
        }

        $parameters = new ExportParameters(
            new PersonalWorkspace($calendar_event_publication->get_publication_publisher()),
            $this->getUser()->getId(),
            ContentObjectExport::FORMAT_ICAL,
            array($calendar_event_publication->get_content_object_id())
        );

        $exporter = ContentObjectExportController::factory($parameters);
        $exporter->download();
    }
}
