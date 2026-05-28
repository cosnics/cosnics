<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException;
use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\CommonEntityRepositoryTrait;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\Storage\Service\Tree\UuidAwareEntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupRepository extends NestedTreeRepository
{
    use CommonEntityRepositoryTrait;

    public function __construct(
        EntityManagerInterface $em, ClassMetadata $class, protected QueryBuilderConfigurator $queryBuilderConfigurator
    )
    {
        parent::__construct(new UuidAwareEntityManager($em), $class);
    }

    public function countDescendantsByGroup(Group $group, bool $recursive = true): int
    {
        return $this->childCount($group, !$recursive);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function countGroups(?ConditionInterface $condition = null): int
    {
        return $this->countEntities(Group::class, new StorageParameters(condition: $condition));
    }

    public function countSiblingsByGroup(Group $group): int
    {
        return $this->childCount($group->getParent(), true) - 1;
    }

    public function findAncestorsAsPathString(
        Group $group, bool $includeSelf = true, string $separator = ' <span class="text-primary">></span> '
    ): string
    {
        return $this->getPathAsString(
            $group, ['includeNode' => $includeSelf, 'separator' => $separator]
        );
    }

    public function findAncestorsByGroup(Group $group, bool $includeSelf = true): ArrayCollection
    {
        return new ArrayCollection($this->getPath($group, ['includeNode' => $includeSelf]));
    }

    public function findAncestorsIdentifiersByGroup(Group $group, bool $includeSelf = true): array
    {
        $ancestors = $this->findAncestorsByGroup($group, $includeSelf);
        $ancestorIdentifiers = [];

        foreach ($ancestors as $ancestor) {
            $ancestorIdentifiers[] = $ancestor->getIdentifier();
        }

        return $ancestorIdentifiers;
    }

    public function findDescendantsByGroup(Group $group, bool $recursive = false): ArrayCollection
    {
        return new ArrayCollection($this->children($group, !$recursive));
    }

    public function findDescendantsIdentifiersByGroup(Group $group, bool $includeSelf = true): array
    {
        $descendants = $this->findDescendantsByGroup($group, $includeSelf);
        $descendantIdentifiers = [];

        foreach ($descendants as $descendant) {
            $descendantIdentifiers[] = $descendant->getIdentifier();
        }

        return $descendantIdentifiers;
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function findGroupByCode(string $code): Group
    {
        $group = $this->findOneBy([Group::PROPERTY_CODE => $code]);

        if (!$group instanceof Group) {
            throw new NoSuchGroupException([Group::PROPERTY_CODE => $code]);
        }

        return $group;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function findGroupByCodeAndParentIdentifier(string $groupCode, ?Uuid $parentIdentifier = null): Group
    {
        try {
            $conditions = [];

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE),
                new StaticConditionVariable($groupCode)
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT),
                new StaticConditionVariable($parentIdentifier)
            );

            return $this->findEntity(
                Group::class, new StorageParameters(condition: new OrCondition($conditions))
            );
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchGroupException(
                $exception->criteria, $exception->query, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function findGroupByIdentifier(Uuid $identifier): Group
    {
        try {
            return $this->findEntityByIdentifier(Group::class, $identifier);
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchGroupException(
                $exception->criteria, $exception->query, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroups(
        ?ConditionInterface $condition = null, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $parameters = new StorageParameters(condition: $condition, orderBy: $orderBy, count: $count, offset: $offset);

        return $this->findEntities(Group::class, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupsByIdentifiers(array $groupIdentifiers, OrderBy $orderBy = new OrderBy()): ArrayCollection
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID), $groupIdentifiers
        );

        return $this->findEntities(
            Group::class, new StorageParameters(
                condition: $condition, orderBy: $orderBy
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupsBySearchQueryAndParentIdentifier(
        ?string $searchQuery = null, ?Uuid $parentIdentifier = null
    ): ArrayCollection
    {
        $conditions = [];

        if ($searchQuery && $searchQuery != '') {
            $searchQueryConditionGenerator = new SearchQueryConditionGenerator();

            $conditions[] = $searchQueryConditionGenerator->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME),
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE)
                ]
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT),
            new StaticConditionVariable($parentIdentifier)
        );

        $condition = new AndCondition($conditions);

        return $this->findEntities(
            Group::class, new StorageParameters(
                condition: $condition, orderBy: new OrderBy(
                [new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))]
            )
            )
        );
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function findRootGroup(): ?Group
    {
        $group = $this->findOneBy([Group::PROPERTY_LEVEL => 0]);

        if (!$group instanceof Group) {
            throw new NoSuchGroupException([Group::PROPERTY_LEVEL => 0]);
        }

        return $group;
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function getGroupReference(Uuid $groupIdentifier): Group
    {
        return $this->getReference(Group::class, $groupIdentifier);
    }

    public function hasDescendantsByGroup(Group $group, bool $recursive = true): bool
    {
        return $this->childCount($group, !$recursive) > 0;
    }

    public function removeGroup(Group $group, bool $flush = true): void
    {
        $this->removeEntity($group, $flush);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveGroup(Group $group, bool $flush = true): void
    {
        $this->saveEntity($group, $flush);
    }
}