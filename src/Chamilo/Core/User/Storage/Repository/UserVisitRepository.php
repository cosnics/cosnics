<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException;
use Chamilo\Core\User\Storage\Entity\UserVisit;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserVisitRepository extends EntityRepository
{
    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException
     */
    public function findUserVisitByIdentifier(string $identifier): ?UserVisit
    {
        $userVisit = $this->find($identifier);

        if (!$userVisit instanceof UserVisit) {
            throw new NoSuchUserVisitException(['identifier' => $identifier]);
        }

        return $userVisit;
    }

    public function saveUserVisit(UserVisit $userVisit): void
    {
        $this->getEntityManager()->persist($userVisit);
        $this->getEntityManager()->flush();
    }
}