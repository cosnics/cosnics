<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator\DataClassGenerator;

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
     * @param string $location - The location of the class
     * @param string $classname the classname
     * @param array of strings $properties the properties
     * @param string $package the package
     * @param string $description the description
     * @param string $author, the author
     */
    public function generate_data_class($location, $classname, $properties, $package, $description, $author, 
        $application_name)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(__DIR__);
        
        if (! is_dir($location))
            mkdir($location, 0777, true);

        $file = fopen(
            $location (string) StringUtilities :: getInstance()->createString($classname)->underscored() . '.class.php',
            'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('dataclass' => 'data_class.template'));
            
            $property_names = array();
            
            $this->template->assign_vars(
                array(
                    'PACKAGE' => $package, 
                    'DESCRIPTION' => $description, 
                    'AUTHOR' => $author, 
                    'OBJECT_CLASS' => $classname, 
                    'L_OBJECT_CLASS' => (string) StringUtilities::getInstance()->createString($classname)->underscored(), 
                    'APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application_name)->upperCamelize(), 
                    'NAMESPACE' => 'application\\' . $application_name));
            
            foreach ($properties as $property)
            {
                $property_const = 'PROPERTY_' . strtoupper($property);
                $property_names[] = 'self :: ' . $property_const;
                
                $this->template->assign_block_vars(
                    "CONSTS", 
                    array('PROPERTY_CONST' => $property_const, 'PROPERTY_NAME' => $property));
                
                $this->template->assign_block_vars(
                    "PROPERTY", 
                    array('PROPERTY_CONST' => $property_const, 'PROPERTY_NAME' => $property));
            }
            
            $this->template->assign_vars(
                array(
                    'DEFAULT_PROPERTY_NAMES' => implode(', ', $property_names), 
                    'APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application_name)->upperCamelize()));
            
            $string = $this->template->pparse_return('dataclass');
            fwrite($file, $string);
            fclose($file);
        }
    }
}
