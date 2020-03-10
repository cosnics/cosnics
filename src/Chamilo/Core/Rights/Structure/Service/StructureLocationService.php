<?php
namespace Chamilo\Core\Rights\Structure\Service;

use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationServiceInterface;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRepositoryInterface;
use Exception;

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
     * Creates a structure location based on a given context and action
     * 
     * @param string $context
     * @param string $action
     *
     * @return StructureLocation
     *
     * @throws \Exception
     */
    public function createStructureLocation($context, $action = null)
    {
        $structureLocation = new StructureLocation();
        
        $structureLocation->setContext($context);
        $structureLocation->setAction($action);
        
        if (! $this->structureLocationRepository->create($structureLocation))
        {
            throw new Exception(
                'The structure location with context ' . $context . ' and action ' . $action . ' could not be created');
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
    public function deleteStructureLocation(StructureLocation $structureLocation)
    {
        if (! $this->structureLocationRepository->delete($structureLocation))
        {
            throw new Exception(
                'The structure location with context ' . $structureLocation->getContext() . ' and action ' .
                     $structureLocation->getAction() . ' could not be deleted');
        }
    }

    /**
     * Truncates the structure locations with their roles
     * 
     * @throws \Exception
     */
    public function truncateStructureLocations()
    {
        if (! $this->structureLocationRepository->truncateStructureLocationsAndRoles())
        {
            throw new Exception('The structure locations and their roles could not be truncated');
        }
    }

    /**
     * Returns the structure location by a given context and action
     * 
     * @param string $context
     * @param string $action
     *
     * @return StructureLocation
     *
     * @throws \Exception
     */
    public function getStructureLocationByContextAndAction($context, $action = null)
    {
        $structureLocation = $this->structureLocationRepository->findStructureLocationByContextAndAction(
            $context, 
            $action);
        
        if (! $structureLocation instanceof StructureLocation)
        {
            throw new Exception(
                'Could not find a structure location with context ' . $context . ' and action ' . $action);
        }
        
        return $structureLocation;
    }
}