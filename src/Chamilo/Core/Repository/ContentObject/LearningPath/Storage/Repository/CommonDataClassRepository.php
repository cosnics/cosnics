<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * Base repository class for data classes
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CommonDataClassRepository
{
    protected DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @throws \Exception
     */
    public function create(DataClass $dataClass): bool
    {
        return $dataClass->create();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function delete(DataClass $dataClass): bool
    {
        return $dataClass->delete();
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @throws \Exception
     */
    public function update(DataClass $dataClass): bool
    {
        return $dataClass->update();
    }

}