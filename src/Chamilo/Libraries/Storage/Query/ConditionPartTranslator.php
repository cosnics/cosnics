<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;

/**
 * @package Chamilo\Libraries\Storage\Query
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionPartTranslator
{
    protected ConditionPartTranslatorService $conditionPartTranslatorService;

    private StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        ConditionPartTranslatorService $conditionPartTranslatorService, StorageAliasGenerator $storageAliasGenerator
    )
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    public function getConditionClass(): string
    {
        return static::CONDITION_CLASS;
    }

    public function getConditionPartTranslatorService(): ConditionPartTranslatorService
    {
        return $this->conditionPartTranslatorService;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }
}
