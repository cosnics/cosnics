<?php

namespace Chamilo\Application\Lti\Storage\Repository;

use Chamilo\Application\Lti\Storage\Entity\Provider;
use Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Application\Lti\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProviderRepository extends EntityRepository
{
    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\Provider $provider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveProvider(Provider $provider)
    {
        $this->getEntityManager()->persist($provider);

        foreach($provider->getCustomParameters() as $customParameter)
        {
            $this->getEntityManager()->persist($customParameter);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param string $uuid
     *
     * @return \Chamilo\Application\Lti\Storage\Entity\Provider|object|null
     */
    public function getProviderByUUID(string $uuid)
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter $customParameter
     * @param bool $flush
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteCustomParameter(ProviderCustomParameter $customParameter, $flush = true)
    {
        $this->getEntityManager()->remove($customParameter);

        if($flush)
        {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\Provider $provider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteProvider(Provider $provider)
    {
        foreach($provider->getCustomParameters() as $customParameter)
        {
            $this->getEntityManager()->remove($customParameter);
        }

        $this->getEntityManager()->remove($provider);
        $this->getEntityManager()->flush();
    }

    /**
     * @return Provider[]
     */
    public function findProviders()
    {
        return $this->findAll();
    }

}