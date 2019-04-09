<?php

namespace Chamilo\Application\Lti\Service;

use Chamilo\Application\Lti\Storage\Entity\LtiProvider;
use Chamilo\Application\Lti\Storage\Repository\LtiProviderRepository;
use Chamilo\Libraries\Utilities\UUID;

/**
 * @package Chamilo\Application\Lti\Service\Launch
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LtiProviderService
{
    /**
     * @var LtiProviderRepository
     */
    protected $ltiProviderRepository;

    /**
     * LtiProviderService constructor.
     *
     * @param \Chamilo\Application\Lti\Storage\Repository\LtiProviderRepository $ltiProviderRepository
     */
    public function __construct(LtiProviderRepository $ltiProviderRepository)
    {
        $this->ltiProviderRepository = $ltiProviderRepository;
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\LtiProvider $ltiProvider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveLtiProvider(LtiProvider $ltiProvider)
    {
        if(empty($ltiProvider->getUuid()))
        {
            $ltiProvider->setUuid(UUID::v4());
        }

        $this->ltiProviderRepository->saveLtiProvider($ltiProvider);
    }

    /**
     * @param string $url
     * @param string $key
     * @param string $secret
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createLtiProviderFromData(string $url, string $key, string $secret)
    {
        $ltiProvider = new LtiProvider();

        $ltiProvider->setLtiUrl($url);
        $ltiProvider->setKey($key);
        $ltiProvider->setSecret($secret);
        $ltiProvider->setUuid(UUID::v4());

        $this->ltiProviderRepository->saveLtiProvider($ltiProvider);
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\LtiProvider $ltiProvider
     * @param string $url
     * @param string $key
     * @param string $secret
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateLtiProviderFromData(LtiProvider $ltiProvider, string $url, string $key, string $secret)
    {
        $ltiProvider->setLtiUrl($url);
        $ltiProvider->setKey($key);
        $ltiProvider->setSecret($secret);

        $this->ltiProviderRepository->saveLtiProvider($ltiProvider);
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\LtiProvider $ltiProvider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteLtiProvider(LtiProvider $ltiProvider)
    {
        $this->ltiProviderRepository->deleteLtiProvider($ltiProvider);
    }

    /**
     * @param int $id
     *
     * @return LtiProvider|object|null
     */
    public function getLtiProviderById(int $id)
    {
        return $this->ltiProviderRepository->find($id);
    }

    /**
     * @param string $uuid
     *
     * @return LtiProvider|object|null
     */
    public function getLtiProviderByUUID(string $uuid)
    {
        return $this->ltiProviderRepository->getLtiProviderByUUID($uuid);
    }

    /**
     * @return mixed
     */
    public function findLtiProviders()
    {
        return $this->ltiProviderRepository->findLtiProviders();
    }


}