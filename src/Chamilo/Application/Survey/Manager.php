<?php
namespace Chamilo\Application\Survey;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 *
 * @package Chamilo\Application\Survey
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'publication_action';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_SELECTED_PUBLICATION_ID = 'selected_publication_id';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_CREATE = 'Creator';
    const ACTION_UPDATE = 'Updater';
    const ACTION_RIGHTS = 'Rights';
    const ACTION_APPLICATION_RIGHTS = 'ApplicationRights';
    const ACTION_SHARE = 'Share';
    const ACTION_UNSHARE = 'Unshare';
    const ACTION_PUBLISH = 'Publisher';
    const ACTION_BROWSE_PERSONAL = 'PersonalBrowser';
    const ACTION_BROWSE_SHARED = 'SharedBrowser';
    const ACTION_FAVOURITE = 'Favourite';
    const ACTION_VIEW = 'Viewer';
    const ACTION_MAIL = 'Mailer';
    const ACTION_TAKE = 'Taker';
    const ACTION_REPORT = 'Reporting';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_FAVOURITE;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function getCurrentPublication()
    {
        $publicationService = new PublicationService(new PublicationRepository());
        $publicationId = $this->getCurrentPublicationIdentifier();
        if ($publicationId)
        {
            return $publicationService->getPublicationByIdentifier($this->getCurrentPublicationIdentifier());
        }
        else
        {
            $publication = new Publication();
            $publication->setId(0);
            $publication->setPublisherId(0);
            return $publication;
        }
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\mixed
     */
    public function getCurrentPublicationIdentifier()
    {
        return $this->getRequest()->query->get(\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID);
    }
}
