<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Component;

use Chamilo\Application\Weblcms\Bridge\Presence\PresenceServiceBridge;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;
//use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Core\Repository\ContentObject\Presence\Display\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Presence\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class DisplayComponent extends Manager implements DelegateComponent
{
    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        $publication = $this->getContentObjectPublication();
        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

        $breadcrumbTrail = BreadcrumbTrail::getInstance();
        $breadcrumbTrail->add(new Breadcrumb($this->get_url(), $publication->getContentObject()->get_title()));

        $configuration = new ApplicationConfiguration(
            $this->get_application()->getRequest(), $this->getUser(), $this
        );

        $this->buildBridges($publication);
        $applicationFactory = $this->getApplicationFactory();
        $applicationFactory->setPresenceServiceBridge(
            $this->getBridgeManager()->getBridgeByInterface(PresenceServiceBridgeInterface::class)
        );

        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Presence\Display\Manager::context(),
            $configuration
        )->run();
    }

    protected function buildBridges(ContentObjectPublication $contentObjectPublication)
    {
        /** @var EvaluationEntityServiceManager $evaluationEntityServiceManager */
        //$evaluationEntityServiceManager = $this->getService(EvaluationEntityServiceManager::class);
        //$evaluationEntityServiceManager->addEntityService(1, $this->getService(CourseGroupEntityService::class));

        // /** @var PublicationUserEntityService $publicationUserEntityService */
        //$publicationUserEntityService = $this->getService(PublicationUserEntityService::class);
        //$publicationUserEntityService->setContentObjectPublication($contentObjectPublication);

        /** @var PresenceServiceBridge $presenceServiceBridge */
        $presenceServiceBridge = $this->getService(PresenceServiceBridge::class);
        $presenceServiceBridge->setCanEditPresence(
            $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $contentObjectPublication)
        );

        $presencePublication = $this->getPresencePublication($contentObjectPublication);
        $presenceServiceBridge->setContentObjectPublication($contentObjectPublication);
        $presenceServiceBridge->setPresencePublication($presencePublication);
        $presenceServiceBridge->setCourse($this->get_course());

        $this->getBridgeManager()->addBridge($presenceServiceBridge);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getContentObjectPublication()
    {
        $contentObjectPublicationId =
            $this->getRequest()->getFromUrl(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        $contentObjectPublicationTranslation =
            $this->getTranslator()->trans('ContentObjectPublication', [], \Chamilo\Application\Weblcms\Tool\Implementation\Presence\Manager::context());

        if (empty($contentObjectPublicationId))
        {
            throw new NoObjectSelectedException($contentObjectPublicationTranslation);
        }

        $contentObjectPublication =
            DataManager::retrieve_by_id(ContentObjectPublication::class_name(), $contentObjectPublicationId);

        if (!$contentObjectPublication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException($contentObjectPublicationTranslation, $contentObjectPublicationId);
        }

        return $contentObjectPublication;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function get_root_content_object()
    {
        return $this->getContentObjectPublication()->getContentObject();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Presence\Display\ApplicationFactory
     */
    public function getApplicationFactory()
    {
        return new ApplicationFactory($this->getRequest(), StringUtilities::getInstance(), Translation::getInstance());
    }

    /**
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
