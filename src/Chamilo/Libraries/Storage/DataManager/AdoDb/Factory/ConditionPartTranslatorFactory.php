<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Factory;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionPart;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConditionPartTranslatorFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    protected $classNameUtilities;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classNameUtilities
     */
    public function __construct(ClassnameUtilities $classNameUtilities)
    {
        $this->classNameUtilities = $classNameUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassNameUtilities()
    {
        return $this->classNameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classNameUtilities
     */
    public function setClassNameUtilities($classNameUtilities)
    {
        $this->classNameUtilities = $classNameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     * @return \Chamilo\Libraries\Storage\Query\ConditionPartTranslator
     */
    public function getConditionPartTranslator(ConditionPartTranslatorService $conditionPartTranslatorService,
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart)
    {
        $className = '\Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart\\' .
             $this->getClassNameUtilities()->getClassnameFromObject($conditionPart) . 'Translator';

        return new $className($conditionPartTranslatorService, $dataClassDatabase, $conditionPart);
    }
}