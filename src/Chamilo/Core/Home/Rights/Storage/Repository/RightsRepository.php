<?php

namespace Chamilo\Core\Home\Rights\Storage\Repository;

/**
 * Database repository for the rights entities
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsRepository
{
    /**
     * @var RightsRepository
     */
    protected $rightsRepository;

    /**
     * BlockTypeRightsService constructor.
     *
     * @param RightsRepository $rightsRepository
     */
    public function __construct(RightsRepository $rightsRepository)
    {
        $this->rightsRepository = $rightsRepository;
    }

    
}