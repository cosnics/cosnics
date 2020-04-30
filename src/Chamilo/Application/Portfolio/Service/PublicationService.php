<?php

namespace Chamilo\Application\Portfolio\Service;

use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Application\Portfolio\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationService
{

    /**
     *
     * @var \Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository
     */
    private $publicationRepository;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Service\RightsService
     */
    private $rightsService;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     */
    private $templateRegistrationConsulter;

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository $publicationRepository
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     * @param \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter $templateRegistrationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        PublicationRepository $publicationRepository, RightsService $rightsService,
        TemplateRegistrationConsulter $templateRegistrationConsulter, Translator $translator
    )
    {
        $this->publicationRepository = $publicationRepository;
        $this->rightsService = $rightsService;
        $this->templateRegistrationConsulter = $templateRegistrationConsulter;
        $this->translator = $translator;
    }

    /**
     * @param $contentObjectIdentifier
     *
     * @return integer
     */
    public function countPublicationsForContentObjectIdentifier(int $contentObjectIdentifier)
    {
        return $this->countPublicationsForContentObjectIdentifiers([$contentObjectIdentifier]);
    }

    /**
     * @param integer[] $contentObjectIdentifiers
     *
     * @return integer
     */
    public function countPublicationsForContentObjectIdentifiers(array $contentObjectIdentifiers)
    {
        return $this->getPublicationRepository()->countPublicationsForContentObjectIdentifiers(
            $contentObjectIdentifiers
        );
    }

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countPublicationsForTypeAndIdentifier(
        $type = PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier,
        Condition $condition = null
    )
    {
        if ($type !== PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT &&
            $type !== PublicationAggregatorInterface::ATTRIBUTES_TYPE_USER)
        {
            return 0;
        }
        else
        {
            return $this->getPublicationRepository()->countPublicationsForTypeAndIdentifier(
                $type, $objectIdentifier, $condition
            );
        }
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     */
    public function createPublication(Publication $publication)
    {
        return $this->getPublicationRepository()->createPublication($publication);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function createRootPortfolioAndPublicationForUser(User $user)
    {
        $portfolio = $this->createRootPortfolioForUser($user);
        $publication = $this->getPublicationInstanceForPortfolioAndUser($portfolio, $user);

        if (!$this->createPublication($publication))
        {
            throw new NotAllowedException();
        }

        $this->getRightsService()->createRightsForEveryUserAtPortfolioRoot($publication->getId(), $portfolio);

        return $publication;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio
     */
    public function createRootPortfolioForUser(User $user)
    {
        $templateRegistration =
            $this->getTemplateRegistrationConsulter()->getTemplateRegistrationDefaultByType(Portfolio::package());

        $portfolio = new Portfolio();
        $portfolio->set_title($user->get_fullname());
        $portfolio->set_description(
            $this->translator->trans('NoInstructionYetDescription', [], 'Chamilo\Application\Portfolio')
        );
        $portfolio->set_owner_id($user->getId());

        $portfolio->set_template_registration_id($templateRegistration->getId());
        $portfolio->create();

        return $portfolio;
    }

    /**
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function deletePublication(Publication $publication)
    {
        // TODO: Delete dependencies: Feedback, Notifications, RightsLocation, RightsLocationEntityRight
        return $this->getPublicationRepository()->deletePublication($publication);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return boolean
     */
    public function deletePublicationByIdentifier(int $publicationIdentifier)
    {
        $publication = $this->findPublicationByIdentifier($publicationIdentifier);

        if ($publication instanceof Publication)
        {
            return $this->deletePublication($publication);
        }
        else
        {
            return false;
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function deletePublicationsForContentObject(ContentObject $contentObject)
    {
        $publications = $this->findPublicationsForContentObjectIdentifier($contentObject->getId());

        foreach ($publications as $publication)
        {
            if (!$this->deletePublication($publication))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function findPublicationByIdentifier(int $publicationIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationByIdentifier($publicationIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function findPublicationForUser(User $user)
    {
        return $this->findPublicationForUserIdentifier($user->getId());
    }

    /**
     *
     * @param integer $userIdentifier
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function findPublicationForUserIdentifier($userIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationForUserIdentifier($userIdentifier);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return string[]
     * @throws \Exception
     */
    public function findPublicationRecordByIdentifier(int $publicationIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationRecordByIdentifier($publicationIdentifier);
    }

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return string[]
     */
    public function findPublicationRecordsForTypeAndIdentifier(
        $type = PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier,
        Condition $condition = null, $count = null, $offset = null, $orderProperties = null
    )
    {
        if ($type !== PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT &&
            $type !== PublicationAggregatorInterface::ATTRIBUTES_TYPE_USER)
        {
            return [];
        }
        else
        {
            return $this->getPublicationRepository()->findPublicationRecordsForTypeAndIdentifier(
                $type, $objectIdentifier, $condition, $count, $offset, $orderProperties
            );
        }
    }

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication[]
     */
    public function findPublicationsForContentObjectIdentifier(int $contentObjectIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationsForContentObjectIdentifier($contentObjectIdentifier);
    }

    /**
     *
     * @param integer $contentObjectIdentifier
     * @param integer $publisherIdentifier
     * @param integer $published
     * @param integer $modified
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function getPublicationInstanceForParameters(
        $contentObjectIdentifier, $publisherIdentifier, $published, $modified
    )
    {
        $publication = new Publication();

        $publication->set_content_object_id($contentObjectIdentifier);
        $publication->set_publisher_id($publisherIdentifier);
        $publication->set_published($published);
        $publication->set_modified($modified);

        return $publication;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio $portfolio
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function getPublicationInstanceForPortfolioAndUser(Portfolio $portfolio, User $user)
    {
        return $this->getPublicationInstanceForParameters($portfolio->getId(), $user->getId(), time(), time());
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository()
    {
        return $this->publicationRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository $publicationRepository
     */
    public function setPublicationRepository(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->rightsService;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService)
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     */
    public function getTemplateRegistrationConsulter(): TemplateRegistrationConsulter
    {
        return $this->templateRegistrationConsulter;
    }

    /**
     * @param \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter $templateRegistrationConsulter
     */
    public function setTemplateRegistrationConsulter(
        TemplateRegistrationConsulter $templateRegistrationConsulter
    ): void
    {
        $this->templateRegistrationConsulter = $templateRegistrationConsulter;
    }

    /**
     *
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function updatePublication(Publication $publication)
    {
        return $this->getPublicationRepository()->updatePublication($publication);
    }
}