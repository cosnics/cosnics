<?php
namespace Chamilo\Libraries\Storage\DataClass\Interfaces;

/**
 * @package Chamilo\Libraries\Storage\DataClass\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface DataClassTypeAwareInterface
{
    public const PROPERTY_TYPE = 'type';

    public function getType(): string;

    //public static function getTypeDataClassName(): string;

    public function setType(string $type): static;
}