<?php
namespace Chamilo\Libraries\Utilities\ContentObjectGenerator\AdditionalClassGenerator;

/**
 * Dataclass generator used to generate dataclasses with given properties
 * 
 * @author Sven Vanpoucke
 */
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\StringUtilities;

class AdditionalClassGenerator
{

    private $xml_definition;

    private $author;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     *
     * @return the $xml_definition
     */
    public function get_xml_definition()
    {
        return $this->xml_definition;
    }

    /**
     *
     * @return the $author
     */
    public function get_author()
    {
        return $this->author;
    }

    /**
     *
     * @param $xml_definition the $xml_definition to set
     */
    public function set_xml_definition($xml_definition)
    {
        $this->xml_definition = $xml_definition;
    }

    /**
     *
     * @param $author the $author to set
     */
    public function set_author($author)
    {
        $this->author = $author;
    }

    /**
     * Generate a data class display file
     * 
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    public function generate_data_class_display()
    {
        $template = new MyTemplate();
        $template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $this->xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/' . $this->xml_definition['name'] . '_display.class.php', 'w+');
        
        if ($file)
        {
            $template->set_filenames(array('data_class_display' => 'data_class_display.template'));
            
            $classname = (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->upperCamelize();
            
            $description = 'This class can be used to display ' .
                 (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->humanize() . 's';
            
            $template->assign_vars(
                array(
                    'TYPE' => $this->xml_definition['name'], 
                    'DESCRIPTION' => $description, 
                    'AUTHOR' => $this->author, 
                    'OBJECT_CLASS' => $classname));
            
            $string = "<?php
namespace libraries\utilities;\n" .
                 $template->pparse_return('data_class_display') . "?>";
            fwrite($file, $string);
            fclose($file);
        }
    }

    /**
     * Generate a data class difference file
     * 
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    public function generate_data_class_difference()
    {
        $template = new MyTemplate();
        $template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $this->xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/' . $this->xml_definition['name'] . '_difference.class.php', 'w+');
        
        if ($file)
        {
            $template->set_filenames(array('data_class_difference' => 'data_class_difference.template'));
            
            $classname = (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->upperCamelize();
            $description = 'This class can be used to get the difference between ' .
                 (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->humanize() . 's';
            
            $template->assign_vars(
                array(
                    'TYPE' => $this->xml_definition['name'], 
                    'DESCRIPTION' => $description, 
                    'AUTHOR' => $this->author, 
                    'OBJECT_CLASS' => $classname));
            
            $string = "<?php
namespace libraries\utilities;\n" .
                 $template->pparse_return('data_class_difference') . "?>";
            fwrite($file, $string);
            fclose($file);
        }
    }

    /**
     * Generate a data class difference display file
     * 
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    public function generate_data_class_difference_display()
    {
        $template = new MyTemplate();
        $template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $this->xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/' . $this->xml_definition['name'] . '_difference_display.class.php', 'w+');
        
        if ($file)
        {
            $template->set_filenames(array('data_class_difference_display' => 'data_class_difference_display.template'));
            
            $classname = (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->upperCamelize();
            $description = 'This class can be used to display the difference between ' .
                 (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->humanize() . 's';
            
            $template->assign_vars(
                array(
                    'TYPE' => $this->xml_definition['name'], 
                    'DESCRIPTION' => $description, 
                    'AUTHOR' => $this->author, 
                    'OBJECT_CLASS' => $classname));
            
            $string = "<?php
namespace libraries\utilities;\n" .
                 $template->pparse_return('data_class_difference_display') . "?>";
            fwrite($file, $string);
            fclose($file);
        }
    }

    /**
     * Generate a complex data class file
     * 
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    public function generate_complex_data_class()
    {
        $template = new MyTemplate();
        $template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $this->xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/complex_' . $this->xml_definition['name'] . '.class.php', 'w+');
        
        if ($file)
        {
            $template->set_filenames(array('complex_data_class' => 'complex_data_class.template'));
            $classname = (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->upperCamelize();
            $template->assign_vars(
                array('TYPE' => $this->xml_definition['name'], 'AUTHOR' => $this->author, 'OBJECT_CLASS' => $classname));
            
            $string = "<?php
namespace libraries\utilities;\n" .
                 $template->pparse_return('complex_data_class') . "?>";
            fwrite($file, $string);
            fclose($file);
        }
    }

    /**
     * Generate a complex data class form file
     * 
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    public function generate_complex_data_class_form()
    {
        $template = new MyTemplate();
        $template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $this->xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/complex_' . $this->xml_definition['name'] . '_form.class.php', 'w+');
        
        if ($file)
        {
            $template->set_filenames(array('complex_data_class_form' => 'complex_data_class_form.template'));
            $classname = (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->upperCamelize();
            $template->assign_vars(
                array('TYPE' => $this->xml_definition['name'], 'AUTHOR' => $this->author, 'OBJECT_CLASS' => $classname));
            
            $string = "<?php
namespace libraries\utilities;\n" .
                 $template->pparse_return('complex_data_class_form') . "?>";
            fwrite($file, $string);
            fclose($file);
        }
    }

    /**
     */
    public function generate_data_class_installer()
    {
        $template = new MyTemplate();
        $template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $this->xml_definition['name'] . '/install';
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/' . $this->xml_definition['name'] . '_installer.class.php', 'w+');
        
        if ($file)
        {
            $template->set_filenames(array('data_class_installer' => 'data_class_installer.template'));
            $classname = (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->upperCamelize();
            $description = 'This class is used to install the ' .
                 (string) StringUtilities :: getInstance()->createString($this->xml_definition['name'])->humanize() .
                 ' content object';
            
            $template->assign_vars(
                array(
                    'TYPE' => $this->xml_definition['name'], 
                    'DESCRIPTION' => $description, 
                    'AUTHOR' => $this->author, 
                    'OBJECT_CLASS' => $classname));
            
            $string = "<?php
namespace libraries\utilities;\n" .
                 $template->pparse_return('data_class_installer') . "?>";
            fwrite($file, $string);
            fclose($file);
        }
    }
}
