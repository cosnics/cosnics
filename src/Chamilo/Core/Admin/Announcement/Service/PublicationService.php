<?php
namespace Chamilo\Core\Admin\Announcement\Service;

use Chamilo\Core\Admin\Announcement\Rights;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\RightsLocation;
use Chamilo\Core\Admin\Announcement\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

class PublicationService
{
    /**
     *
     * @var \Chamilo\Core\Admin\Announcement\Storage\Repository\PublicationRepository
     */
    private $publicationRepository;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\Repository\PublicationRepository $publicationRepository
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        PublicationRepository $publicationRepository, Translator $translator
    )
    {
        $this->publicationRepository = $publicationRepository;
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
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function createPublication(Publication $publication)
    {
        if (!$this->getPublicationRepository()->createPublication($publication))
        {
            return false;
        }

        $rightsService = Rights::getInstance();

        return $rightsService->create_location(
            self::package(), Rights::TYPE_PUBLICATION, $publication->getId(), false,
            $rightsService->get_root_id(self::package())
        );
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function deletePublication(Publication $publication)
    {
        $rightsLocation = Rights::getInstance()->get_location_by_identifier(
            'Chamilo\Core\Admin\Announcement', Rights::TYPE_PUBLICATION, $publication->getId()
        );

        if ($rightsLocation instanceof RightsLocation)
        {
            if (!$rightsLocation->delete())
            {
                return false;
            }
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
     * @return \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication
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
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
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
     * @return \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication[]
     */
    public function findPublicationsForContentObjectIdentifier(int $contentObjectIdentifier)
    {
        return $this->getPublicationRepository()->findPublicationsForContentObjectIdentifier($contentObjectIdentifier);
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication
     */
    public function getPublicationInstance()
    {
        $publication = new Publication();
        $publication->set_publication_date(time());

        return $publication;
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository(): PublicationRepository
    {
        return $this->publicationRepository;
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\Repository\PublicationRepository $publicationRepository
     */
    public function setPublicationRepository(PublicationRepository $publicationRepository): void
    {
        $this->publicationRepository = $publicationRepository;
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
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function updatePublication(Publication $publication)
    {
        return $this->getPublicationRepository()->updatePublication($publication);
    }
}