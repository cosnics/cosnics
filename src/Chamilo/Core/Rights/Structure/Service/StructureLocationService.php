<?php

namespace Chamilo\Core\Rights\Structure\Service;

use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationServiceInterface;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRepositoryInterface;

/**
 * Manages structure locations
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationService implements StructureLocationServiceInterface
{
    /**
     * @var StructureLocationRepositoryInterface
     */
    protected $structureLocationRepository;

    /**
     * StructureLocationService constructor.
     *
     * @param StructureLocationRepositoryInterface $structureLocationRepository
     */
    public function __construct(StructureLocationRepositoryInterface $structureLocationRepository)
    {
        $this->structureLocationRepository = $structureLocationRepository;
    }

    /**
     * Creates a structure location based on a given context and component
     *
     * @param string $context
     * @param string $component
     *
     * @return StructureLocation
     *
     * @throws \Exception
     */
    public function createStructureLocation($context, $component = null)
    {
        $structureLocation = new StructureLocation();

        $structureLocation->setContext($context);
        $structureLocation->setComponent($component);

        if (!$structureLocation->create())
        {
            throw new \Exception(
                'The structure location with context ' . $context . ' and component ' . $component .
                ' could not be created'
            );
        }

        return $structureLocation;
    }

    /**
     * Deletes a given structure location
     *
     * @param StructureLocation $structureLocation
     *
     * @throws \Exception
     */
    public function deleteRole(StructureLocation $structureLocation)
    {
        if (!$structureLocation->delete())
        {
            throw new \Exception(
                'The structure location with context ' . $structureLocation->getContext() . ' and component ' .
                $structureLocation->getComponent() . ' could not be deleted'
            );
        }
    }

    /**
     * Returns the structure location by a given context and component
     *
     * @param string $context
     * @param string $component
     *
     * @return StructureLocation
     *
     * @throws \Exception
     */
    public function getStructureLocationByContextAndComponent($context, $component = null)
    {
        $structureLocation = $this->structureLocationRepository->findStructureLocationByContextAndComponent(
            $context, $component
        );

        if (!$structureLocation instanceof StructureLocation)
        {
            throw new \Exception(
                'Could not find a structure location with context ' . $context . ' and component ' . $component
            );
        }

        return $structureLocation;
    }
}