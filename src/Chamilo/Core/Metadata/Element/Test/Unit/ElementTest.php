<?php
namespace Chamilo\Core\Metadata\Element\Test\Unit;

use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\DataClassTest;

/**
 * Tests the element data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementTest extends DataClassTest
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
        return new Element();
    }
}