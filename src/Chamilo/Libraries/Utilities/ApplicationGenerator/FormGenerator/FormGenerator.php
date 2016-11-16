<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator\FormGenerator;

use Chamilo\Libraries\Utilities\Utilities;

/**
 * Dataclass generator used to generate a form for an object
 * 
 * @author Sven Vanpoucke
 */
class FormGenerator
{

    private $template;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Generate a form with the given info
     * 
     * @param string $location - The location of the class
     * @param string $object_name - The name of the object
     * @param string $properties - The properties of the object
     * @param string $author - The author
     */
    public function generate_form($location, $object_name, $properties, $author, $application_name)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(__DIR__);
        
        if (! is_dir($location))
            mkdir($location, 0777, true);

        $file = fopen(
            $location (string) StringUtilities :: getInstance()->createString($object_name)->underscored() . '_form.class.php',
            'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('form' => 'form.template'));
            
            $this->template->assign_vars(
                array(
                    'OBJECT_CLASS' => $object_name, 
                    'L_OBJECT_CLASS' => (string) StringUtilities::getInstance()->createString($object_name)->underscored(), 
                    'AUTHOR' => $author, 
                    'NAMESPACE' => 'application\\' . $application_name));
            
            foreach ($properties as $property)
            {
                $property_lower = (string) StringUtilities::getInstance()->createString($property)->underscored();
                $property_camelcase = (string) StringUtilities::getInstance()->createString($property)->upperCamelize();
                $property_const = 'PROPERTY_' . strtoupper($property);
                
                $this->template->assign_block_vars(
                    "PROPERTIES", 
                    array(
                        'PROPERTY' => $property_const, 
                        'PROPERTY_L' => $property_lower, 
                        'PROPERTY_C' => $property_camelcase));
            }
            
            $string = trim($this->template->pparse_return('form'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}
