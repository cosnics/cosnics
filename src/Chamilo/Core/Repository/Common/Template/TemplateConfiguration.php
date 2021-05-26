<?php
namespace Chamilo\Core\Repository\Common\Template;

use DOMXPath;
use Exception;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TemplateConfiguration
{
    const ACTION_EDIT = 'edit';

    private $storage;

    public function __construct($storage = array())
    {
        $this->storage = $storage;
    }

    public function get_storage()
    {
        return $this->storage;
    }

    public function set_storage($storage)
    {
        $this->storage = $storage;
    }

    /**
     *
     * @param string $property
     * @param int $action
     * @param boolean $configuration
     */
    public function set_configuration($property, $action, $configuration)
    {
        $this->storage[$property][$action] = $configuration;
    }

    /**
     *
     * @param string $property
     * @param int $action
     */
    public function get_configuration($property, $action)
    {
        return $this->storage[$property][$action];
    }

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return TemplateConfiguration
     * @throws Exception
     */
    public static function get(DOMXPath $dom_xpath)
    {
        $template_configuration_class_name = $dom_xpath->query('/template')->item(0)->getAttribute('context') .
             '\Template\TemplateConfiguration';
        
        if (! is_subclass_of(
            $template_configuration_class_name, 
            'Chamilo\Core\Repository\Common\Template\TemplateConfigurationParser'))
        {
            throw new Exception(
                $template_configuration_class_name .
                     ' doesn\'t seem to support parsing, please implement the Chamilo\Core\Repository\Common\Template\TemplateConfigurationParser interface');
        }
        else
        {
            return $template_configuration_class_name::parse($dom_xpath);
        }
    }
}