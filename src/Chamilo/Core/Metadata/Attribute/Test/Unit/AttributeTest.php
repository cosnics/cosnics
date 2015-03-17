<?php
namespace Chamilo\Core\Metadata\Attribute\Test\Unit;

use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Libraries\Storage\DataClassTest;

/**
 * Tests the schema data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttributeTest extends DataClassTest
{

    /**
     * **************************************************************************************************************
     * Inherited functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the object for the current tested dataclass
     * 
     * @return DataClass
     */
    protected function get_data_class_object()
    {
        return new Attribute();
    }
}