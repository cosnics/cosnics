<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: extensions.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * 
 * @package admin.lib.package_installer.dependency
 */
class ExtensionsDependency extends Dependency
{

    public function check()
    {
        $message = Translation :: get('DependencyCheckExtension') . ': ' . $this->as_html();
        $this->logger->add_message($message);
        
        return extension_loaded($this->get_id());
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
        return $this->get_id();
    }
}
