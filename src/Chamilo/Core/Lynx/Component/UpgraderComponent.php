<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Core\Lynx\Manager\Action\PackageUpgrader;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

class UpgraderComponent extends Manager implements NoAuthenticationSupport
{

    private $packages = array();

    public function run()
    {
        set_time_limit(0);
        ini_set("memory_limit", - 1);

        $this->initialize();

        $html = array();

        $html[] = Display :: header();

        while (($package = $this->get_next_package()) != null)
        {
            $package_upgrader = new PackageUpgrader($package, false);
            $success = $package_upgrader->run();

            $upgrader = $package_upgrader->get_upgrader();
            if ($upgrader)
            {
                $this->add_packages($upgrader->get_additional_packages());
            }

            $image = Theme :: getInstance()->getImagePath($package, 'Logo/48');
            $title = Translation :: get('TypeName', null, $package);
            $result = $package_upgrader->get_result(true);

            $html[] = $this->render_upgrade_step($image, $title, $result);

            flush();

            if (! $success)
            {
                break;
            }
        }

        if ($success)
        {
            $html[] = $this->upgrade_successfull();
        }

        $html[] = ResourceManager :: getInstance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Lynx', true) . 'LynxProcess.js');

        $html[] = Display :: footer($this);

        return implode(PHP_EOL, $html);
    }

    public function render_upgrade_step($image, $title, $result)
    {
        $html = array();

        $html[] = '<div class="package_upgrade upgrade-step-collapsed" style="background-image: url(' . $image . ');">';
        $html[] = '<div class="package"><div class="title">' . $title . '</div></div>';
        $html[] = '<div class="description">';
        $html[] = $result;
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return multitype:string
     */
    public function get_packages()
    {
        return $this->packages;
    }

    /**
     *
     * @param multitype:string $packages
     */
    public function set_packages($packages)
    {
        $this->packages = $packages;
    }

    /**
     *
     * @param string $context
     */
    public function add_package($context)
    {
        array_push($this->packages, $context);
    }

    /**
     *
     * @param multitype:string $packages
     */
    public function add_packages($packages)
    {
        foreach ($packages as $package)
        {
            $this->add_package($package);
        }
    }

    /**
     *
     * @return string
     */
    public function get_next_package()
    {
        return array_shift($this->packages);
    }

    public function initialize()
    {
        $this->add_package('\Chamilo\Configuration');
        $this->add_packages(Application :: get_packages_from_filesystem(Registration :: TYPE_CORE));
    }

    public function upgrade_successfull()
    {
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Lynx', 'PackageAction/Finished');
        $title = Translation :: get('PlatformUpgraded');
        $result = Translation :: get(
            'CoreUpgraded',
            array('URL' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONTENT_OBJECT_UPGRADE))));

        return $this->render_upgrade_step($image, $title, $result);
    }
}
