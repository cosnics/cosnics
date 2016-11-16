<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionPartTranslator
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService
     */
    private $conditionPartTranslatorService;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface
     */
    private $dataClassDatabase;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\ConditionPart
     */
    private $conditionPart;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     */
    public function __construct(ConditionPartTranslatorService $conditionPartTranslatorService, 
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart)
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->dataClassDatabase = $dataClassDatabase;
        $this->conditionPart = $conditionPart;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService
     */
    public function getConditionPartTranslatorService()
    {
        return $this->conditionPartTranslatorService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     */
    public function setConditionPartTranslatorService($conditionPartTranslatorService)
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface
     */
    public function getDataClassDatabase()
    {
        return $this->dataClassDatabase;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     */
    public function setDataClassDatabase($dataClassDatabase)
    {
        $this->dataClassDatabase = $dataClassDatabase;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\ConditionPart
     */
    public function getConditionPart()
    {
        return $this->conditionPart;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     */
    public function setConditionPart($conditionPart)
    {
        $this->conditionPart = $conditionPart;
    }

    /**
     *
     * @return string
     */
    abstract public function translate();
}
