<?php
namespace Chamilo\Libraries\Storage\Architecture\Interface;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConditionVariableInterface extends HashableInterface
{
    public function getConditionVariableTranslatorClass(): string;
}