<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Version;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Abstract class that describes a chamilo package depedency, can be used for all elements (applications, objects,
 * tools, subapplications) from chamilo
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package admin.lib.package_installer.dependency
 */
class RegistrationDependency extends Dependency
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

    /**
     * Creates a dependency information string as html
     * 
     * @return String
     */
    public function as_html()
    {
        $parameters = array();
        $parameters['OPERATOR'] = $this->get_version()->get_operator_name();
        $parameters['ID'] = Translation::get('TypeName', null, $this->get_id());
        $parameters['VERSION'] = $this->get_version()->get_release();
        
        return Translation::get('RegistrationDependency', $parameters);
    }

    /**
     * Checks the dependency in the registration table of the administration
     * 
     * @return boolean
     */
    public function check()
    {
        $parameters = array();
        $parameters['REQUIREMENT'] = $this->as_html();
        
        $message = Translation::get('DependencyCheckRegistration') . ': ' . $this->as_html() . ' ' . Translation::get(
            'Found', 
            array(), 
            Utilities::COMMON_LIBRARIES) . ': ';
        $registration = \Chamilo\Configuration\Configuration::registration($this->get_id());
        
        if (empty($registration))
        {
            $parameters['CURRENT'] = '--' . Translation::get('Nothing', array(), Utilities::COMMON_LIBRARIES) . '--';
            $this->logger->add_message(
                Translation::get('CurrentRegistrationDependency', $parameters), 
                MessageLogger::TYPE_ERROR);
            return false;
        }
        else
        {
            $target_version = Version::compare(
                $this->get_version()->get_operator(), 
                $this->get_version()->get_release(), 
                $registration[Registration::PROPERTY_VERSION]);
            
            if (! $target_version)
            {
                $parameters['CURRENT'] = '--' .
                     Translation::get('WrongVersion', array(), Utilities::COMMON_LIBRARIES) . '--';
                $this->logger->add_message(
                    Translation::get('CurrentRegistrationDependency', $parameters), 
                    MessageLogger::TYPE_ERROR);
                return false;
            }
            else
            {
                if (! $registration[Registration::PROPERTY_STATUS])
                {
                    $parameters['CURRENT'] = '--' . Translation::get(
                        'InactiveObject', 
                        array(), 
                        Utilities::COMMON_LIBRARIES) . '--';
                    $this->logger->add_message(
                        Translation::get('CurrentRegistrationDependency', $parameters), 
                        MessageLogger::TYPE_ERROR);
                    return false;
                }
                else
                {
                    $this->logger->add_message($parameters['REQUIREMENT']);
                    return true;
                }
            }
        }
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function needs($context)
    {
        return $this->get_id() == $context;
    }

    public static function dom_node($dom_xpath, $dom_node)
    {
        $dependency = parent::dom_node($dom_xpath, $dom_node);
        
        $version_node = $dom_xpath->query('version', $dom_node)->item(0);
        $version = new Version($version_node->nodeValue, $version_node->getAttribute('operator'));
        
        $dependency->set_version($version);
        return $dependency;
    }
}
