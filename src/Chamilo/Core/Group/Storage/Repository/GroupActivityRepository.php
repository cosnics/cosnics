<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\Entity\GroupActivity;
use Chamilo\Libraries\Storage\Architecture\Trait\CommonEntityRepositoryTrait;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupActivityRepository extends EntityRepository
{
    use CommonEntityRepositoryTrait;

    public function __construct(
        EntityManagerInterface $em, ClassMetadata $class, protected QueryBuilderConfigurator $queryBuilderConfigurator
    )
    {
        parent::__construct($em, $class);
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function getGroupActivityReference(Uuid $groupIdentifier): GroupActivity
    {
        return $this->getReference(GroupActivity::class, $groupIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveGroupActivity(GroupActivity $groupActivity, bool $flush = true): void
    {
        $this->saveEntity($groupActivity, $flush);
    }
}