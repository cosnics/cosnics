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

    private ConditionPart $conditionPart;

    private ConditionPartTranslatorServiceInterface $conditionPartTranslatorService;

    private DataClassDatabaseInterface $dataClassDatabase;

    public function __construct(
        ConditionPartTranslatorServiceInterface $conditionPartTranslatorService,
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart
    )
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->dataClassDatabase = $dataClassDatabase;
        $this->conditionPart = $conditionPart;
    }

    public function getConditionPart(): ConditionPart
    {
        return $this->conditionPart;
    }

    public function setConditionPart(ConditionPart $conditionPart): ConditionPartTranslator
    {
        $this->conditionPart = $conditionPart;

        return $this;
    }

    public function getConditionPartTranslatorService(): ConditionPartTranslatorServiceInterface
    {
        return $this->conditionPartTranslatorService;
    }

    public function setConditionPartTranslatorService(
        ConditionPartTranslatorServiceInterface $conditionPartTranslatorService
    ): ConditionPartTranslator
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;

        return $this;
    }

    public function getDataClassDatabase(): DataClassDatabaseInterface
    {
        return $this->dataClassDatabase;
    }

    public function setDataClassDatabase(DataClassDatabaseInterface $dataClassDatabase): ConditionPartTranslator
    {
        $this->dataClassDatabase = $dataClassDatabase;

        return $this;
    }

    abstract public function translate(?bool $enableAliasing = true): string;
}
