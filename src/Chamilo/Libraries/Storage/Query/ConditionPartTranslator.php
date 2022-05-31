<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionPartTranslator
{

    private StorageAliasGenerator $storageAliasGenerator;

    public function __construct(StorageAliasGenerator $storageAliasGenerator)
    {
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    //abstract public function translate($conditionPart, ?bool $enableAliasing = true): string;
}
