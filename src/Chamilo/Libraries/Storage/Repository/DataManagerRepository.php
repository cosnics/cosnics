<?php
namespace Chamilo\Libraries\Storage\Repository;

use Chamilo\Libraries\Storage\Architecture\Interfaces\DataManagerRepositoryInterface;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract repository that can be used as a base for repositories that still use the old DataManagers.
 *
 * @package Chamilo\Libraries\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataManagerRepository implements DataManagerRepositoryInterface
{

    public function create(DataClass $dataClass): bool
    {
        return $dataClass->create();
    }

    public function delete(DataClass $dataClass): bool
    {
        return $dataClass->delete();
    }

    public function update(DataClass $dataClass): bool
    {
        return $dataClass->update();
    }
}