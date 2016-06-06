<?php

namespace Chamilo\Core\Home\Rights\Service;

use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;

/**
 * Service to manage the rights for the given block types
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeRightsService
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