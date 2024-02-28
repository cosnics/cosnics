<?php
namespace Chamilo\Libraries\Storage\DataClass\Interfaces;

/**
 * @package Chamilo\Libraries\Storage\DataClass\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface DataClassBaseExtensionInterface
{
    public static function getExtensionDataClassName(): string;
}