<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException;
use Chamilo\Core\User\Storage\Entity\UserVisit;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
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
class UserVisitRepository extends EntityRepository
{
    use CommonEntityRepositoryTrait;

    public function __construct(
        EntityManagerInterface $em, ClassMetadata $class, protected QueryBuilderConfigurator $queryBuilderConfigurator
    )
    {
        parent::__construct($em, $class);
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException
     */
    public function findUserVisitByIdentifier(Uuid $identifier): UserVisit
    {
        try {
            return $this->findEntityByIdentifier(UserVisit::class, $identifier);
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchUserVisitException(
                $exception->criteria, $exception->query, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function getUserVisitReference(Uuid $identifier): UserVisit
    {
        return $this->getReference(UserVisit::class, $identifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveUserVisit(UserVisit $userVisit, bool $flush = true): void
    {
        $this->saveEntity($userVisit, $flush);
    }
}