<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Version;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: server.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * 
 * @package admin.lib.package_installer.dependency
 */
class ServerDependency extends Dependency
{
    const PROPERTY_VERSION = 'version';

    /**
     *
     * @var \configuration\package\Version
     */
    private $version;

    /**
     *
     * @return \configuration\package\Version
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     *
     * @param \configuration\package\Version $version
     */
    public function set_version($version)
    {
        $this->version = $version;
    }

    public function check()
    {
        $parameters = array();
        $parameters['REQUIREMENT'] = $this->as_html();
        
        switch ($this->get_id())
        {
            case 'php' :
                $parameters['CURRENT'] = phpversion();
                $result = Version :: compare(
                    $this->get_version()->get_operator(), 
                    $this->get_version()->get_release(), 
                    phpversion());
                break;
            default :
                $result = false;
        }
        
        if ($result)
        {
            $this->logger->add_message($parameters['REQUIREMENT']);
        }
        else
        {
            $this->logger->add_message(Translation :: get('CurrentServerDependency', $parameters));
        }
        
        return $result;
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function needs($context)
    {
        return false;
    }

    public function as_html()
    {
        $parameters = array();
        $parameters['ID'] = $this->get_id();
        $parameters['OPERATOR'] = $this->get_version()->get_operator_name();
        $parameters['VERSION'] = $this->get_version()->get_release();
        
        return Translation :: get('ServerDependency', $parameters);
    }

    public static function dom_node($dom_xpath, $dom_node)
    {
        $dependency = parent :: dom_node($dom_xpath, $dom_node);
        
        $version_node = $dom_xpath->query('version', $dom_node)->item(0);
        $version = new Version($version_node->nodeValue, $version_node->getAttribute('operator'));
        
        $dependency->set_version($version);
        return $dependency;
    }
}
