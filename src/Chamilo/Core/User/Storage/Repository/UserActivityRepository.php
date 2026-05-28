<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\Entity\UserActivity;
use Chamilo\Libraries\Storage\Architecture\Trait\CommonEntityRepositoryTrait;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserActivityRepository extends EntityRepository
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
    public function getUserActivityReference(Uuid $identifier): UserActivity
    {
        return $this->getReference(UserActivity::class, $identifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveUserActivity(UserActivity $userActivity, bool $flush = true): void
    {
        $this->saveEntity($userActivity, $flush);
    }
}