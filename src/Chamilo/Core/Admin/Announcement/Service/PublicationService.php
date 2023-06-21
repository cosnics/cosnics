<?php
namespace Chamilo\Core\Admin\Announcement\Service;

use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Storage\Repository\PublicationRepository;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

class PublicationService
{

    protected DatetimeUtilities $datetimeUtilities;

    private GroupEntityProvider $groupEntityProvider;

    private PublicationRepository $publicationRepository;

    private RightsService $rightsService;

    private Translator $translator;

    private UserEntityProvider $userEntityProvider;

    public function __construct(
        PublicationRepository $publicationRepository, Translator $translator, RightsService $rightsService,
        UserEntityProvider $userEntityProvider, GroupEntityProvider $groupEntityProvider,
        DatetimeUtilities $datetimeUtilities
    )
    {
        $this->publicationRepository = $publicationRepository;
        $this->translator = $translator;
        $this->rightsService = $rightsService;
        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
        $this->datetimeUtilities = $datetimeUtilities;
    }

    public function countPublications(?Condition $condition = null): int
    {
        return $this->getPublicationRepository()->countPublications($condition);
    }

    public function countPublicationsForContentObjectIdentifier(string $contentObjectIdentifier): int
    {
        return $this->countPublicationsForContentObjectIdentifiers([$contentObjectIdentifier]);
    }

    /**
     * @param string[] $contentObjectIdentifiers
     */
    public function countPublicationsForContentObjectIdentifiers(array $contentObjectIdentifiers): int
    {
        return $this->getPublicationRepository()->countPublicationsForContentObjectIdentifiers(
            $contentObjectIdentifiers
        );
    }

    public function countPublicationsForTypeAndIdentifier(
        int $type, string $objectIdentifier, ?Condition $condition = null
    ): int
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

    public function countVisiblePublicationsForUserIdentifier(string $userIdentifier, ?Condition $condition = null): int
    {
        $publicationIdentifiers = $this->getRightsService()->findPublicationIdentifiersWithViewRightForUserIdentifier(
            $userIdentifier
        );

        return $this->getPublicationRepository()->countVisiblePublicationsForPublicationIdentifiers(
            $publicationIdentifiers, $condition
        );
    }

    public function createPublication(Publication $publication): bool
    {
        $publication->set_publication_date(time());
        $publication->set_modification_date($publication->get_publication_date());

        if (!$this->getPublicationRepository()->createPublication($publication))
        {
            return false;
        }

        return $this->getRightsService()->createPublicationRightsLocation($publication);
    }

    /**
     * @param string[] $values
     */
    public function createPublicationForUserIdentifierAndContentObjectIdentifierFromValues(
        string $userIdentifier, string $contentObjectIdentifier, array $values
    ): ?Publication
    {
        $publication = new Publication();
        $publication->set_content_object_id($contentObjectIdentifier);
        $publication->set_publisher_id($userIdentifier);

        if (!$this->savePublicationFromValues($publication, $userIdentifier, $values))
        {
            return null;
        }

        return $publication;
    }

    /**
     * @param string[] $contentObjectIdentifiers
     * @param string[] $values
     */
    public function createPublicationsForUserIdentifierAndContentObjectIdentifiersFromValues(
        string $userIdentifier, array $contentObjectIdentifiers, array $values
    ): bool
    {
        foreach ($contentObjectIdentifiers as $contentObjectIdentifier)
        {
            $publication = $this->createPublicationForUserIdentifierAndContentObjectIdentifierFromValues(
                $userIdentifier, $contentObjectIdentifier, $values
            );

            if (!$publication instanceof Publication)
            {
                return false;
            }
        }

        return true;
    }

    public function deletePublication(Publication $publication): bool
    {
        if (!$this->getRightsService()->deletePublicationRightsLocation($publication))
        {
            return false;
        }

        return $this->getPublicationRepository()->deletePublication($publication);
    }

    public function deletePublicationByIdentifier(string $publicationIdentifier): bool
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function deletePublicationsForContentObject(ContentObject $contentObject): bool
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

    public function findPublicationByIdentifier(string $publicationIdentifier): ?Publication
    {
        return $this->getPublicationRepository()->findPublicationByIdentifier($publicationIdentifier);
    }

    /**
     * @return string[]
     */
    public function findPublicationRecordByIdentifier(string $publicationIdentifier): array
    {
        return $this->getPublicationRepository()->findPublicationRecordByIdentifier($publicationIdentifier);
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPublicationRecords(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getPublicationRepository()->findPublicationRecords($condition, $count, $offset, $orderBy);
    }

    /**
     * @param int $type
     * @param string $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPublicationRecordsForTypeAndIdentifier(
        int $type, string $objectIdentifier, ?Condition $condition = null, ?int $count = null, ?int $offset = null,
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
     * @param string $contentObjectIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPublicationsForContentObjectIdentifier(string $contentObjectIdentifier): ArrayCollection
    {
        return $this->getPublicationRepository()->findPublicationsForContentObjectIdentifier($contentObjectIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findVisiblePublicationRecordsForUserIdentifier(
        string $userIdentifier, Condition $condition = null, int $count = null, int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $publicationIdentifiers =
            $this->getRightsService()->findPublicationIdentifiersWithViewRightForUserIdentifier($userIdentifier);

        return $this->getPublicationRepository()->findVisiblePublicationRecordsForPublicationIdentifiers(
            $publicationIdentifiers, $condition, $count, $offset, $orderBy
        );
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    /**
     * @return \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    public function getGroupEntityProvider(): GroupEntityProvider
    {
        return $this->groupEntityProvider;
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository(): PublicationRepository
    {
        return $this->publicationRepository;
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Service\RightsService
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
     * @return \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    public function getUserEntityProvider(): UserEntityProvider
    {
        return $this->userEntityProvider;
    }

    public function savePublication(Publication $publication): bool
    {
        if ($publication->isIdentified())
        {
            return $this->updatePublication($publication);
        }
        else
        {
            return $this->createPublication($publication);
        }
    }

    public function savePublicationFromValues(Publication $publication, string $userIdentifier, array $values): bool
    {
        if ($values[FormValidator::PROPERTY_TIME_PERIOD_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $datetimeUtilities = $this->getDatetimeUtilities();

            $from = $datetimeUtilities->timeFromDatepicker($values[Publication::PROPERTY_FROM_DATE]);
            $to = $datetimeUtilities->timeFromDatepicker($values[Publication::PROPERTY_TO_DATE]);
        }

        $publication->set_from_date($from);
        $publication->set_to_date($to);
        $publication->set_hidden($values[Publication::PROPERTY_HIDDEN] ? 1 : 0);

        if (!$this->savePublication($publication))
        {
            return false;
        }

        return $this->getRightsService()->updatePublicationRights($publication, $userIdentifier, $values);
    }

    public function updatePublication(Publication $publication): bool
    {
        $publication->set_modification_date(time());

        return $this->getPublicationRepository()->updatePublication($publication);
    }
}