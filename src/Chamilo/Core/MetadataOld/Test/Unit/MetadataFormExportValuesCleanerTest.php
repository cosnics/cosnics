<?php
namespace Chamilo\Core\MetadataOld\Test\Unit;

use Chamilo\Core\MetadataOld\MetadataFormExportValuesCleaner;
use Chamilo\Libraries\Test\Test;

/**
 * Tests the MetadataFormExportValuesCleaner
 * 
 * @package core\metadata\test
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataFormExportValuesCleanerTest extends Test
{

    /**
     * Tests the clean export values method
     */
    public function test_clean_export_values()
    {
        $export_values = array('md$element$attribute' => 'test');
        
        $export_values_cleaner = new MetadataFormExportValuesCleaner();
        $cleaned_export_values = $export_values_cleaner->clean_export_values($export_values);
        
        $this->assertEquals($cleaned_export_values['md']['element']['attribute'], 'test');
    }

    /**
     * Tests the clean export values with simple export values and without the array delimiter
     */
    public function test_clean_export_values_with_simple_export_values()
    {
        $export_values = array('md' => 'test');
        
        $export_values_cleaner = new MetadataFormExportValuesCleaner();
        $cleaned_export_values = $export_values_cleaner->clean_export_values($export_values);
        
        $this->assertEquals($cleaned_export_values['md'], 'test');
    }

    /**
     * Tests the clean export values with a real array and without the array delimiter
     */
    public function test_clean_export_values_with_real_array()
    {
        $export_values = array('md' => array('element' => 'test'));
        
        $export_values_cleaner = new MetadataFormExportValuesCleaner();
        $cleaned_export_values = $export_values_cleaner->clean_export_values($export_values);
        
        $this->assertEquals($cleaned_export_values['md']['element'], 'test');
    }

    /**
     * Tests the clean export values with a real array and an array delimiter
     */
    public function test_clean_export_values_with_real_array_and_array_delimiter()
    {
        $export_values = array('md' => array('nested_element' => 'test1'), 'md$element$attribute' => 'test');
        
        $export_values_cleaner = new MetadataFormExportValuesCleaner();
        $cleaned_export_values = $export_values_cleaner->clean_export_values($export_values);
        
        $this->assertEquals(count($cleaned_export_values['md']), 2);
    }

    /**
     * Tests the clean export values with an invalid parameter which is no array
     */
    public function test_clean_export_values_with_no_array()
    {
        $export_values_cleaner = new MetadataFormExportValuesCleaner();
        $cleaned_export_values = $export_values_cleaner->clean_export_values(null);
        
        $this->assertEquals($cleaned_export_values, array());
    }
}