<?php
namespace Chamilo\Configuration\Form\Table\Element;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

/**
 * Table for the schema
 * 
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTable extends DataClassListTableRenderer
{
    const TABLE_IDENTIFIER = Manager::PARAM_DYNAMIC_FORM_ID;
}