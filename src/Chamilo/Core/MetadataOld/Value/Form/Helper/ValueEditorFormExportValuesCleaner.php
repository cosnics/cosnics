<?php
namespace Chamilo\Core\MetadataOld\Value\Form\Helper;

/**
 * This class cleans the export data from the quickform to support multiple arrays based on the $ delimiter
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ValueEditorFormExportValuesCleaner
{

    /**
     * Cleans the export values
     * Quickform does not support a multi dimensional array, the form uses $ to determine a multi-array,
     * this function parses the export values and returns a multi dimensional array
     * 
     * @param array $export_values
     *
     * @return array
     */
    public function clean_export_values(array $export_values = array())
    {
        $new_export_values = array();
        
        foreach ($export_values as $key => $values)
        {
            if (is_array($values))
            {
                $values = $this->clean_export_values($values);
            }
            
            $dynamic_array_parts = array_reverse(explode('$', $key));
            
            foreach ($dynamic_array_parts as $dynamic_array_part)
            {
                $values = array($dynamic_array_part => $values);
            }
            
            $new_export_values = array_merge_recursive($new_export_values, $values);
        }
        
        return $new_export_values;
    }
}