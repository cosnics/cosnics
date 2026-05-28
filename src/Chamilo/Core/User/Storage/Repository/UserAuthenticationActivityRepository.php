<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\Entity\UserAuthenticationActivity;
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
class UserAuthenticationActivityRepository extends EntityRepository
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
    public function getUserAuthenticationActivityReference(Uuid $identifier): UserAuthenticationActivity
    {
        return $this->getReference(UserAuthenticationActivity::class, $identifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveUserAuthenticationActivity(
        UserAuthenticationActivity $userAuthenticationActivity, bool $flush = true
    ): void
    {
        $this->saveEntity($userAuthenticationActivity, $flush);
    }
}