<?php
namespace Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes a relation with a controlled vocabulary
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ControlledVocabularyRelation extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_CONTROLLED_VOCABULARY_ID = 'controlled_vocabulary_id';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Get the default properties
     * 
     * @param array $extended_property_names
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTROLLED_VOCABULARY_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the controlled_vocabulary_id
     * 
     * @return int - the controlled_vocabulary_id.
     */
    public function get_controlled_vocabulary_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTROLLED_VOCABULARY_ID);
    }

    /**
     * Sets the controlled_vocabulary_id
     * 
     * @param int $controlled_vocabulary_id
     */
    public function set_controlled_vocabulary_id($controlled_vocabulary_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTROLLED_VOCABULARY_ID, $controlled_vocabulary_id);
    }
}