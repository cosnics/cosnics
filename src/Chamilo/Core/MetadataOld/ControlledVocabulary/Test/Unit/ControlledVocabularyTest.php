<?php
namespace Chamilo\Core\MetadataOld\ControlledVocabulary\Test\Unit;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Libraries\Storage\DataClassTest;

/**
 * Tests the schema data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ControlledVocabularyTest extends DataClassTest
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
        return new ControlledVocabulary();
    }
}