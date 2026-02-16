<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Libraries\Calendar\Architecture\Interface\VisibilityServiceInterface;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;

/**
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VisibilityService implements VisibilityServiceInterface
{
    private VisibilityRepository $visibilityRepository;

    public function __construct(VisibilityRepository $visibilityRepository)
    {
        $this->visibilityRepository = $visibilityRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function changeVisibility(string $userIdentifier, string $source): bool
    {
        try {
            $visibility = $this->retrieveVisibilityForUserIdentifierAndSource($userIdentifier, $source);

            return $this->deleteVisibility($visibility);
        }
        catch (StorageNoResultException) {
            return $this->createVisibilityFromParameters($userIdentifier, $source);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function createVisibilityFromParameters(string $userIdentifier, string $source): bool
    {
        $visibility = new Visibility();
        $visibility->setUserId($userIdentifier);
        $visibility->setSource($source);

        return $this->getVisibilityRepository()->createVisibility($visibility);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteVisibility(Visibility $visibility): bool
    {
        return $this->getVisibilityRepository()->deleteVisibility($visibility);
    }

    public function getVisibilityRepository(): VisibilityRepository
    {
        return $this->visibilityRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveVisibilityForUserIdentifierAndSource(string $userIdentifier, string $source): ?Visibility
    {
        return $this->getVisibilityRepository()->retrieveVisibilityForUserIdentifierAndSource($userIdentifier, $source);
    }
}