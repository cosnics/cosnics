<?php
namespace Chamilo\Libraries\Storage\Architecture\Interface;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConditionInterface extends HashableInterface
{
    public function getConditionTranslatorClass(): string;
}