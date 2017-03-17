<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * Base repository class for data classes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CommonDataClassRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * LearningPathChildRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    
}