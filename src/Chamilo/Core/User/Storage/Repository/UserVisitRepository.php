<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException;
use Chamilo\Core\User\Storage\Entity\UserVisit;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserVisitRepository extends AbstractEntityRepository
{
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveUserVisit(UserVisit $userVisit): void
    {
        $this->saveEntity($userVisit);
    }
}