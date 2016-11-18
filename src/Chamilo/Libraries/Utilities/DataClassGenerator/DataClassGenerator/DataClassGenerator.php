<?php
namespace Chamilo\Libraries\Utilities\DataClassGenerator\DataClassGenerator;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Dataclass generator used to generate dataclasses with given properties
 * 
 * @author Sven Vanpoucke
 */
class DataClassGenerator
{

    private $template;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Generate a dataclass with the given info
     * 
     * @param $location string - The location of the class
     * @param $classname string the classname
     * @param array of strings $properties the properties
     * @param $package string the package
     * @param $description string the description
     * @param $author, string the author
     */
    public function generate_data_class($xml_definition, $author, $package, $application)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(__DIR__);
        
        $location = __DIR__ . '/../xml_schemas/php/';
        
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        if (! is_dir(
            $location . ClassnameUtilities::getInstance()->namespaceToPath($xml_definition['namespace']) . '/php/lib/'))
            mkdir(
                $location . ClassnameUtilities::getInstance()->namespaceToPath($xml_definition['namespace']) .
                     '/php/lib/', 
                    0777, 
                    true);
        
        $file = fopen(
            $location . ClassnameUtilities::getInstance()->namespaceToPath($xml_definition['namespace']) . '/php/lib/' .
                 $xml_definition['name'] . '.class.php', 
                'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('dataclass' => 'data_class.template'));
            
            $property_names = array();
            
            $this->template->assign_vars(
                array(
                    'PACKAGE' => str_replace('\\', '.', $xml_definition['namespace']) . '.' . $application, 
                    'AUTHOR' => $author, 
                    'OBJECT_CLASS' => (string) StringUtilities::getInstance()->createString($xml_definition['name'])->upperCamelize(), 
                    'L_OBJECT_CLASS' => $xml_definition['name'], 
                    'APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application)->upperCamelize(), 
                    'NAMESPACE' => $xml_definition['namespace']));
            
            foreach ($xml_definition['properties'] as $property_name => $property_attributes)
            {
                if ($property_name != 'id')
                {
                    $property_const = 'PROPERTY_' . strtoupper($property_name);
                    $property_names[] = 'self :: ' . $property_const;
                    
                    $this->template->assign_block_vars(
                        "CONSTS", 
                        array('PROPERTY_CONST' => $property_const, 'PROPERTY_NAME' => $property_name));
                    
                    $this->template->assign_block_vars("DEFAULT_PROPERTY", array('PROPERTY_CONST' => $property_const));
                    
                    $this->template->assign_block_vars(
                        "PROPERTY", 
                        array(
                            'PROPERTY_CONST' => $property_const, 
                            'PROPERTY_NAME' => $property_name, 
                            'PROPERTY_TYPE' => $property_attributes['type']));
                }
            }
            
            $this->template->assign_vars(
                array(
                    'APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application)->upperCamelize()));
            
            $string = $this->template->pparse_return('dataclass');
            fwrite($file, $string);
            fclose($file);
        }
    }
}
