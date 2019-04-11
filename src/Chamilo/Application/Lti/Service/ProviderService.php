<?php

namespace Chamilo\Application\Lti\Service;

use Chamilo\Application\Lti\Storage\Entity\Provider;
use Chamilo\Application\Lti\Storage\Repository\ProviderRepository;
use Chamilo\Libraries\Utilities\UUID;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Lti\Service\Launch
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProviderService
{
    /**
     * @var ProviderRepository
     */
    protected $providerRepository;

    /**
     * ProviderService constructor.
     *
     * @param \Chamilo\Application\Lti\Storage\Repository\ProviderRepository $providerRepository
     */
    public function __construct(ProviderRepository $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\Provider $provider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveProvider(Provider $provider)
    {
        if(empty($provider->getUuid()))
        {
            $provider->setUuid(UUID::v4());
        }

        $this->providerRepository->saveProvider($provider);
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\Provider $provider
     * @param \Doctrine\Common\Collections\ArrayCollection $originalParameters
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateProvider(Provider $provider, ArrayCollection $originalParameters)
    {
        foreach ($originalParameters as $originalParameter)
        {
            if (false === $provider->getCustomParameters()->contains($originalParameter))
            {
                $originalParameter->clearProvider();
                $this->providerRepository->deleteCustomParameter($originalParameter, false);
            }
        }

        $this->providerRepository->saveProvider($provider);

    }

    /**
     * @param string $url
     * @param string $key
     * @param string $secret
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createProviderFromData(string $url, string $key, string $secret)
    {
        $provider = new Provider();

        $provider->setLtiUrl($url);
        $provider->setKey($key);
        $provider->setSecret($secret);
        $provider->setUuid(UUID::v4());

        $this->providerRepository->saveProvider($provider);
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\Provider $provider
     * @param string $url
     * @param string $key
     * @param string $secret
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateProviderFromData(Provider $provider, string $url, string $key, string $secret)
    {
        $provider->setLtiUrl($url);
        $provider->setKey($key);
        $provider->setSecret($secret);

        $this->providerRepository->saveProvider($provider);
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\Provider $provider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteProvider(Provider $provider)
    {
        $this->providerRepository->deleteProvider($provider);
    }

    /**
     * @param int $id
     *
     * @return Provider
     */
    public function getProviderById(int $id)
    {
        $provider = $this->providerRepository->find($id);
        if(!$provider instanceof Provider)
        {
            throw new \InvalidArgumentException(sprintf('The provider with id %s could not be found', $id));
        }

        return $provider;
    }

    /**
     * @param string $uuid
     *
     * @return Provider
     */
    public function getProviderByUUID(string $uuid)
    {
        $provider = $this->providerRepository->getProviderByUUID($uuid);

        if(!$provider instanceof Provider)
        {
            throw new \InvalidArgumentException(sprintf('The provider with UUID %s could not be found', $uuid));
        }

        return $provider;
    }

    /**
     * @return Provider[]
     */
    public function findProviders()
    {
        return $this->providerRepository->findProviders();
    }
}