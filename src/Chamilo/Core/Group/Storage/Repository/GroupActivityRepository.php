<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\Entity\GroupActivity;
use Chamilo\Libraries\Storage\Architecture\Trait\CommonEntityRepositoryTrait;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createGroupActivity(GroupActivity $groupActivity): void
    {
        $this->saveEntity($groupActivity);
    }
}