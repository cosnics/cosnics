<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\Repository;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\StorageParameters;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return int
     */
    public function countPublicationGroupsForPublication(Publication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_PUBLICATION),
            new StaticConditionVariable($publication->getId())
        );

        return $this->getDataClassRepository()->count(
            PublicationGroup::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return int
     */
    public function countPublicationUsersForPublication(Publication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_PUBLICATION),
            new StaticConditionVariable($publication->getId())
        );

        return $this->getDataClassRepository()->count(
            PublicationUser::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup $publicationGroup
     *
     * @return bool
     */
    public function createPublicationGroup(PublicationGroup $publicationGroup)
    {
        return $this->getDataClassRepository()->create($publicationGroup);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser $publicationUser
     *
     * @return bool
     */
    public function createPublicationUser(PublicationUser $publicationUser)
    {
        return $this->getDataClassRepository()->create($publicationUser);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function deletePublicationGroupsForPublication(Publication $publication)
    {
        return $this->getDataClassRepository()->deletes(
            PublicationGroup::class, new EqualityCondition(
                new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_PUBLICATION),
                new StaticConditionVariable($publication->getId())
            )
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param int $groupIdentifiers
     *
     * @return bool
     */
    public function deletePublicationGroupsForPublicationAndGroupIdentifiers(
        Publication $publication, array $groupIdentifiers
    )
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_PUBLICATION),
            new StaticConditionVariable($publication->getId())
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_GROUP_ID),
            $groupIdentifiers
        );

        return $this->getDataClassRepository()->deletes(
            PublicationGroup::class, new AndCondition($conditions)
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function deletePublicationUsersForPublication(Publication $publication)
    {
        return $this->getDataClassRepository()->deletes(
            PublicationUser::class, new EqualityCondition(
                new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_PUBLICATION),
                new StaticConditionVariable($publication->getId())
            )
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param int $userIdentifiers
     *
     * @return bool
     */
    public function deletePublicationUsersForPublicationAndUserIdentifiers(
        Publication $publication, array $userIdentifiers
    )
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_PUBLICATION),
            new StaticConditionVariable($publication->getId())
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_USER), $userIdentifiers
        );

        return $this->getDataClassRepository()->deletes(
            PublicationUser::class, new AndCondition($conditions)
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Repository\DataClassRepository
     */
    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Repository\DataClassRepository $dataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $dataClassRepository): void
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return int
     */
    public function getPublicationGroupIdentifiersForPublication(Publication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_PUBLICATION),
            new StaticConditionVariable($publication->getId())
        );

        $properties = new RetrieveProperties(
            [new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_GROUP_ID)]
        );

        return $this->getDataClassRepository()->distinct(
            PublicationGroup::class, new StorageParameters(
                condition: $condition, retrieveProperties: $properties
            )
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup[]
     */
    public function getPublicationGroupsForPublication(Publication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_PUBLICATION),
            new StaticConditionVariable($publication->getId())
        );

        return $this->getDataClassRepository()->retrieves(
            PublicationGroup::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return int
     */
    public function getPublicationUserIdentifiersForPublication(Publication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_PUBLICATION),
            new StaticConditionVariable($publication->getId())
        );

        $properties = new RetrieveProperties(
            [new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_USER)]
        );

        return $this->getDataClassRepository()->distinct(
            PublicationUser::class, new StorageParameters(
                condition: $condition, retrieveProperties: $properties
            )
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser[]
     */
    public function getPublicationUsersForPublication(Publication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_PUBLICATION),
            new StaticConditionVariable($publication->getId())
        );

        return $this->getDataClassRepository()->retrieves(
            PublicationUser::class, new StorageParameters(condition: $condition)
        );
    }
}

