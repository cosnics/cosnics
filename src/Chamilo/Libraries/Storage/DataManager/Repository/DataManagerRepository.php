<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;

/**
 * Abstract repository that can be used as a base for repositories that still use the old DataManagers.
 *
 * @package Chamilo\Libraries\Storage\DataManager\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataManagerRepository implements DataManagerRepositoryInterface
{

    /**
     * @throws \ReflectionException
     */
    public function create(DataClass $dataClass): bool
    {
        return $dataClass->create();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     */
    public function delete(DataClass $dataClass): bool
    {
        return $dataClass->delete();
    }

    /**
     * @throws \ReflectionException
     */
    public function update(DataClass $dataClass): bool
    {
        return $dataClass->update();
    }
}