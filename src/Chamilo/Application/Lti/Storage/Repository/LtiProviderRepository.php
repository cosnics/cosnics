<?php

namespace Chamilo\Application\Lti\Storage\Repository;

use Chamilo\Application\Lti\Storage\Entity\LtiProvider;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Application\Lti\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LtiProviderRepository extends EntityRepository
{
    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\LtiProvider $ltiProvider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveLtiProvider(LtiProvider $ltiProvider)
    {
        $this->getEntityManager()->persist($ltiProvider);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $uuid
     *
     * @return \Chamilo\Application\Lti\Storage\Entity\LtiProvider|object|null
     */
    public function getLtiProviderByUUID(string $uuid)
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\LtiProvider $ltiProvider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteLtiProvider(LtiProvider $ltiProvider)
    {
        $this->getEntityManager()->remove($ltiProvider);
        $this->getEntityManager()->flush();
    }

    /**
     * @return LtiProvider[]
     */
    public function findLtiProviders()
    {
        return $this->findAll();
    }

}