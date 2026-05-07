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
    public function __construct(protected VisibilityRepository $visibilityRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function changeVisibility(string $userIdentifier, string $source): void
    {
        try {
            $visibility = $this->retrieveVisibilityForUserIdentifierAndSource($userIdentifier, $source);

            $this->deleteVisibility($visibility);
        }
        catch (StorageNoResultException) {
            $this->createVisibilityFromParameters($userIdentifier, $source);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createVisibilityFromParameters(string $userIdentifier, string $source): void
    {
        $visibility = new Visibility();
        $visibility->setUserId($userIdentifier);
        $visibility->setSource($source);

        $this->visibilityRepository->createVisibility($visibility);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteVisibility(Visibility $visibility): void
    {
        $this->visibilityRepository->deleteVisibility($visibility);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveVisibilityForUserIdentifierAndSource(string $userIdentifier, string $source): ?Visibility
    {
        return $this->visibilityRepository->retrieveVisibilityForUserIdentifierAndSource($userIdentifier, $source);
    }
}