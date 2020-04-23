<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface;
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
     * @var \Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface
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
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface $conditionPartTranslatorService
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     */
    public function __construct(
        ConditionPartTranslatorServiceInterface $conditionPartTranslatorService,
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart
    )
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->dataClassDatabase = $dataClassDatabase;
        $this->conditionPart = $conditionPart;
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
    public function setConditionPart(ConditionPart $conditionPart)
    {
        $this->conditionPart = $conditionPart;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface
     */
    public function getConditionPartTranslatorService()
    {
        return $this->conditionPartTranslatorService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\ConditionPartTranslatorServiceInterface $conditionPartTranslatorService
     */
    public function setConditionPartTranslatorService(
        ConditionPartTranslatorServiceInterface $conditionPartTranslatorService
    )
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
    public function setDataClassDatabase(DataClassDatabaseInterface $dataClassDatabase)
    {
        $this->dataClassDatabase = $dataClassDatabase;
    }

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    abstract public function translate(bool $enableAliasing = true);
}
