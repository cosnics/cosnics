<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * All conditions that aggregate other conditions for DataClass object selection in the data source must extend this
 * class.
 * By using instances of extents of this class itself in other aggregate conditions, you can create complex
 * boolean structures.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
abstract class AggregateCondition extends Condition
{
}
