<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Service;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

class PublicationService
{
    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\PublicationRepository
     */
    private $publicationRepository;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Service\RightsService
     */
    private $rightsService;

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
     * @return boolean
     */
    public function createPublication(Publication $publication)
    {
        return $this->getPublicationRepository()->createPublication($publication);
    }

    /**
     * @param integer $contentObjectIdentifier
     * @param integer $userIdentifier
     * @param integer[] $targetUserIdentifiers
     * @param integer[] $targetGroupIdentifiers
     *
     * @return boolean
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
     * @return boolean
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
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication
     */
    public function findPublicationByIdentifier(int $publicationIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationByIdentifier($publicationIdentifier);
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
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return string[]
     */
    public function findPublicationRecordsForTypeAndIdentifier(
        int $type, int $objectIdentifier, Condition $condition = null, $count = null, $offset = null,
        $orderProperties = null
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
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\PublicationRepository $publicationRepository
     */
    public function setPublicationRepository(PublicationRepository $publicationRepository): void
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
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
     * @return boolean
     */
    public function updatePublication(Publication $publication)
    {
        return $this->getPublicationRepository()->updatePublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $targetUserIdentifiers
     * @param integer[] $targetGroupIdentifiers
     *
     * @return boolean
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