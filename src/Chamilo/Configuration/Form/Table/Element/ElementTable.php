<?php
namespace Chamilo\Configuration\Form\Table\Element;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

/**
 * Table for the schema
 * 
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_DYNAMIC_FORM_ID;
}