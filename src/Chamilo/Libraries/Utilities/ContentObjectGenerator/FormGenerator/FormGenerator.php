<?php
namespace Chamilo\Libraries\Utilities\ContentObjectGenerator\FormGenerator;

/**
 * Dataclass generator used to generate a form for a content object
 * 
 * @author Sven Vanpoucke
 */
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\Utilities;

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
    public function generate_form($xml_definition, $author)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/' . $xml_definition['name'] . '_form.class.php', 'w+');
        
        if ($file)
        {
            $classname = (string) StringUtilities :: getInstance()->createString($xml_definition['name'])->upperCamelize();
            
            $this->template->set_filenames(array('form' => 'form.template'));
            $this->template->assign_vars(
                array('OBJECT_CLASS' => $classname, 'TYPE' => $xml_definition['name'], 'AUTHOR' => $author));
            
            foreach ($xml_definition['properties'] as $property => $attributes)
            {
                if ($property !== 'id')
                {
                    $property_lower = (string) StringUtilities :: getInstance()->createString($property)->underscored();
                    $property_camelcase = (string) StringUtilities :: getInstance()->createString($property)->upperCamelize();
                    $property_const = 'PROPERTY_' . strtoupper($property);
                    
                    $this->template->assign_block_vars(
                        "PROPERTIES", 
                        array(
                            'PROPERTY' => $property_const, 
                            'PROPERTY_LOWER_CASE' => $property_lower, 
                            'PROPERTY_CAMEL_CASE' => $property_camelcase));
                }
            }
            
            $string = trim($this->template->pparse_return('form'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}
