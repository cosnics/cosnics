<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ExporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $publicationIdentifier = $this->getRequest()->query->get(Manager::PARAM_PUBLICATION_ID);

        if (is_null($publicationIdentifier))
        {
            throw new ParameterNotDefinedException(Manager::PARAM_PUBLICATION_ID);
        }

        $publication = $this->getPublicationService()->findPublicationByIdentifier($publicationIdentifier);
        if (!$publication)
        {
            throw new ObjectNotExistException(Translation::get('Publication'), $publicationIdentifier);
        }

        if (!$this->getRightsService()->isAllowedToViewPublication($publication, $this->getUser()))
        {
            throw new NotAllowedException();
        }

        $publisher = $this->getUserService()->findUserByIdentifier($publication->get_publisher());

        $parameters = new ExportParameters(
            $this->getCurrentWorkspace(), $this->getUser()->getId(), ContentObjectExport::FORMAT_ICAL,
            [$publication->get_content_object_id()]
        );

        $exporter = ContentObjectExportController::factory($parameters);
        $exporter->download();
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }
}
