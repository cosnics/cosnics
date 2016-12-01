<?php
namespace Chamilo\Core\Lynx\Package;

use Chamilo\Core\Lynx\Source\DataClass\Source;

class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Runs the install-script.
     */
    public function extra()
    {
        $values = $this->get_form_values();
        
        // Create the initial package sources
        $source = new Source();
        $source->set_name('Official Chamilo Connect Packages');
        $source->set_description(
            'Packages which are officialy supported by the Chamilo Connect community are listed in this repository.');
        $source->set_uri('http://packages.chamilo.org');
        $source->set_status(Source::STATUS_INACTIVE);
        
        if (! $source->create())
        {
            return false;
        }
        
        $source = new Source();
        $source->set_name('Edison Testserver');
        $source->set_description('Testserver for package management development. Hosted by Hogeschool Gent.');
        $source->set_uri('http://edison.hogent.be/package_server');
        $source->set_status(Source::STATUS_ACTIVE);
        
        if (! $source->create())
        {
            return false;
        }
        
        $source = new Source();
        $source->set_name('Localhost');
        $source->set_description('Local test-repository for package management development.');
        $source->set_uri(substr($values['platform_url'], 0, strlen($values['platform_url'] - 1)));
        $source->set_status(Source::STATUS_ACTIVE);
        
        if (! $source->create())
        {
            return false;
        }
        
        return true;
    }
}
