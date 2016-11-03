<?php
namespace Chamilo\Libraries\Utilities\ContentObjectGenerator\PackageInfoGenerator;

/**
 * Package info generator used to generate package info files with given properties
 * 
 * @author Hans De Bisschop
 */
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\StringUtilities;

class PackageInfoGenerator
{

    private $template;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Generate a package.info file
     * 
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    public function generate_package_info($xml_definition, $author)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/package/package.info', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('package_info' => 'package_info.template'));
            
            $name = (string) StringUtilities :: getInstance()->createString($xml_definition['name'])->humanize()->toTitleCase();
            
            $this->template->assign_vars(
                array(
                    'CONTENT_OBJECT_NAME' => $name, 
                    'CODE' => $xml_definition['name'], 
                    'FOLDER' => $xml_definition['name']{0}, 
                    'AUTHOR' => $author));
            
            $string = $this->template->pparse_return('package_info');
            fwrite($file, $string);
            fclose($file);
        }
    }

    /**
     * Generate a settings file
     * 
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    public function generate_settings($xml_definition)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(__DIR__);
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $xml_definition['name'] . '/settings';
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/settings_' . $xml_definition['name'] . '.xml', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('settings' => 'settings.template'));
            
            $name = (string) StringUtilities :: getInstance()->createString($xml_definition['name'])->humanize()->toTitleCase();
            
            $this->template->assign_vars(array('CODE' => $xml_definition['name']));
            
            $string = $this->template->pparse_return('settings');
            fwrite($file, $string);
            fclose($file);
        }
    }
}
