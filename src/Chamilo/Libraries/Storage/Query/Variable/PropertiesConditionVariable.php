<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Exception;

/**
 * A ConditionVariable that describes all the properties of a DataClass
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesConditionVariable extends ConditionVariable
{

    private string $dataClassName;

    /**
     * @throws \Exception
     */
    public function __construct(string $dataClassName)
    {
        if (!class_exists($dataClassName))
        {
            throw new Exception($dataClassName . ' does not exist');
        }

        $this->dataClassName = $dataClassName;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     * @deprecated DO NOT use this anymore!
     */
    public function getAlias(): string
    {
        /**
         * @var \Chamilo\Libraries\Storage\DataClass\DataClass $dataClassName
         */
        $dataClassName = $this->getDataClassName();

        return DataManager::get_alias($dataClassName::getTableName());
    }

    public function getDataClassName(): string
    {
        return $this->dataClassName;
    }

    public function setDataClassName(string $dataClassName): PropertiesConditionVariable
    {
        $this->dataClassName = $dataClassName;

        return $this;
    }

    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->getDataClassName();

        return $hashParts;
    }
}
