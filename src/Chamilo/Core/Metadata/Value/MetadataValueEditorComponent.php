<?php
namespace Chamilo\Core\Metadata\Value;

/**
 * This interface determines that the component supports the necessary methods for the EditorComponent
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface MetadataValueEditorComponent
{

    /**
     * Redirects the user after the update
     * 
     * @param bool $success
     * @param string $message
     */
    public function redirect_after_update($success, $message);

    /**
     * Returns the value creator for the editor
     * 
     * @return ValueCreator
     */
    public function get_value_creator();

    /**
     * Returns the element values
     * 
     * @return ElementValue[]
     */
    public function get_element_values();

    /**
     * Truncates the metadata values
     */
    public function truncate_values();
}