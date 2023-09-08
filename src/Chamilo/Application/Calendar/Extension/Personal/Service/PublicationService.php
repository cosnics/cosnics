<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Service;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

class PublicationService
{
    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\PublicationRepository $publicationRepository
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Calendar\Extension\Personal\Service\RightsService $rightsService
     */
    public function __construct(
        PublicationRepository $publicationRepository, Translator $translator, RightsService $rightsService
    )
    {
        $this->publicationRepository = $publicationRepository;
        $this->translator = $translator;
        $this->rightsService = $rightsService;
    }

    /**
     * @param $contentObjectIdentifier
     *
     * @return int
     */
    public function countPublicationsForContentObjectIdentifier(int $contentObjectIdentifier)
    {
        return $this->countPublicationsForContentObjectIdentifiers([$contentObjectIdentifier]);
    }

    /**
     * @param int $contentObjectIdentifiers
     *
     * @return int
     */
    public function countPublicationsForContentObjectIdentifiers(array $contentObjectIdentifiers)
    {
        return $this->getPublicationRepository()->countPublicationsForContentObjectIdentifiers(
            $contentObjectIdentifiers
        );
    }

    /**
     * @param int $type
     * @param int $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countPublicationsForTypeAndIdentifier(int $type, int $objectIdentifier, Condition $condition = null)
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
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function createPublication(Publication $publication)
    {
        return $this->getPublicationRepository()->createPublication($publication);
    }

    /**
     * @param int $contentObjectIdentifier
     * @param int $userIdentifier
     * @param int $targetUserIdentifiers
     * @param int $targetGroupIdentifiers
     *
     * @return bool
     */
    public function createPublicationWithRightsFromParameters(
        int $contentObjectIdentifier, int $userIdentifier, array $targetUserIdentifiers, array $targetGroupIdentifiers
    )
    {
        $publication = $this->getPublicationInstance();
        $publication->set_content_object_id($contentObjectIdentifier);
        $publication->set_publisher($userIdentifier);

        if ($this->createPublication($publication))
        {
            return $this->getRightsService()->createPublicationRightsForPublicationAndUserAndGroupIdentifiers(
                $publication, $targetUserIdentifiers, $targetGroupIdentifiers
            );
        }

        return false;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function deletePublication(Publication $publication)
    {
        if (!$this->getRightsService()->deletePublicationRights($publication))
        {
            return false;
        }

        return $this->getPublicationRepository()->deletePublication($publication);
    }

    /**
     * @return bool
     */
    public function deletePublicationByIdentifier(string $publicationIdentifier)
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
     * @return bool
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
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication
     */
    public function findPublicationByIdentifier(string $publicationIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationByIdentifier($publicationIdentifier);
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function findPublicationRecordByIdentifier(string $publicationIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationRecordByIdentifier($publicationIdentifier);
    }

    /**
     * @param int $type
     * @param int $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \Exception
     */
    public function findPublicationRecordsForTypeAndIdentifier(
        int $type, int $objectIdentifier, ?Condition $condition = null, ?int $count = null, ?int $offset = null,
        ?OrderBy $orderProperties = null
    ): ArrayCollection
    {
        if ($type !== PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT &&
            $type !== PublicationAggregatorInterface::ATTRIBUTES_TYPE_USER)
        {
            return new ArrayCollection();
        }
        else
        {
            return $this->getPublicationRepository()->findPublicationRecordsForTypeAndIdentifier(
                $type, $objectIdentifier, $condition, $count, $offset, $orderProperties
            );
        }
    }

    /**
     * @param int $contentObjectIdentifier
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication[]
     */
    public function findPublicationsForContentObjectIdentifier(int $contentObjectIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationsForContentObjectIdentifier($contentObjectIdentifier);
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication
     */
    public function getPublicationInstance()
    {
        $publication = new Publication();
        $publication->set_published(time());

        return $publication;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository(): PublicationRepository
    {
        return $this->publicationRepository;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\PublicationRepository $publicationRepository
     */
    public function setPublicationRepository(PublicationRepository $publicationRepository): void
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function updatePublication(Publication $publication)
    {
        return $this->getPublicationRepository()->updatePublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param int $targetUserIdentifiers
     * @param int $targetGroupIdentifiers
     *
     * @return bool
     */
    public function updatePublicationWithRightsFromParameters(
        Publication $publication, array $targetUserIdentifiers, array $targetGroupIdentifiers
    )
    {
        return $this->getRightsService()->updatePublicationRightsForPublicationAndUserAndGroupIdentifiers(
            $publication, $targetUserIdentifiers, $targetGroupIdentifiers
        );
    }
}