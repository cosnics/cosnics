<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Component;

use Chamilo\Application\Weblcms\Bridge\Evaluation\EvaluationServiceBridge;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Component
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
        $applicationFactory->setEvaluationServiceBridge(
            $this->getBridgeManager()->getBridgeByInterface(EvaluationServiceBridgeInterface::class)
        );

        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context(),
            $configuration
        )->run();
    }

    protected function buildBridges(ContentObjectPublication $contentObjectPublication)
    {
        /** @var EvaluationServiceBridge $evaluationServiceBridge */
        $evaluationServiceBridge = $this->getService(EvaluationServiceBridge::class);
        $evaluationServiceBridge->setCanEditEvaluation(
            $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $contentObjectPublication)
        );

        $evaluationPublication = $this->getEvaluationPublication($contentObjectPublication);
        $evaluationServiceBridge->setCurrentEntityType($evaluationPublication->getEntityType());
        $evaluationServiceBridge->setContextIdentifier(new ContextIdentifier(get_class($evaluationPublication), $contentObjectPublication->getId()));
        $evaluationServiceBridge->setReleaseScores($evaluationPublication->getReleaseScores());
        $evaluationServiceBridge->setPublicationId($contentObjectPublication->getId());
        $this->getBridgeManager()->addBridge($evaluationServiceBridge);
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
            $this->getTranslator()->trans('ContentObjectPublication', [], \Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Manager::context());

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
     * @return \Chamilo\Core\Repository\ContentObject\Evaluation\Display\ApplicationFactory
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