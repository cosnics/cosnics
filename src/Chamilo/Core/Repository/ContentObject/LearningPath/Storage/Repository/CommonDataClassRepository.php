<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Libraries\Storage\DataClass\DataClass;
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
     * TreeNodeDataRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /*****************************************************************************************************************
     * Fallback functionality for dataclass methods                                                                  *
     *****************************************************************************************************************/

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function create(DataClass $dataClass)
    {
        return $dataClass->create();
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function update(DataClass $dataClass)
    {
        return $dataClass->update();
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function delete(DataClass $dataClass)
    {
        return $dataClass->delete();
    }
    
}