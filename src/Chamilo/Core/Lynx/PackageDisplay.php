<?php
namespace Chamilo\Core\Lynx;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class PackageDisplay
{

    /**
     *
     * @var \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    private $package_info;

    /**
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     *
     * @throws \Exception
     */
    public function __construct($application)
    {
        $this->application = $application;
        $this->package_info = Package::get($application->get_context());
    }

    public function render()
    {
        $html = [];

        $html[] = $this->get_properties_table();
        $html[] = $this->get_dependencies_table();

        if (!$this->get_registration() instanceof Registration)
        {
            $html[] = $this->get_install_problems();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_application()
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function get_context()
    {
        return $this->get_application()->get_context();
    }

    public function get_dependencies_table()
    {
        $package_info = $this->get_package_info();

        $html = [];

        if ($package_info->has_dependencies())
        {
            $html[] = '<h3>' . Translation::get('Dependencies') . '</h3>';

            $html[] = '<table class="table table-striped table-bordered table-hover table-data">';

            if (!is_null($package_info->get_dependencies()))
            {
                $html[] = '<tr>';
                $html[] = '<td class="header">' . Translation::get('PreDepends') . '</td>';
                $html[] = '<td>' . $package_info->get_dependencies()->as_html() . '</td>';
                $html[] = '</tr>';
            }

            $html[] = '</table><br/>';
        }

        return implode(PHP_EOL, $html);
    }

    public function get_install_problems()
    {
        $package_dependency = new DependencyVerifier($this->get_package_info());
        $success = $package_dependency->is_installable();

        $html = [];

        $html[] = '<h3>' . Translation::get(
                'InstallationDependencies', array('VERSION' => $this->get_package_info()->get_version())
            ) . '</h3>';

        $html[] = '<div class="panel panel-' . ($success ? 'success' : 'danger') . '">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . Translation::get('DependenciesResultVerification') . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $package_dependency->get_logger()->render();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function get_package_info()
    {
        return $this->package_info;
    }

    public function get_properties_table()
    {
        $package_info = $this->get_package_info();

        $html = [];
        $html[] = '<table class="table table-striped table-bordered table-hover table-data data_table_no_header">';
        $properties = $package_info->get_default_property_names();

        $hidden_properties = array(
            Package::PROPERTY_AUTHORS,
            Package::PROPERTY_VERSION,
            Package::PROPERTY_DEPENDENCIES,
            Package::PROPERTY_EXTRA
        );

        foreach ($properties as $property)
        {
            $value = $package_info->get_default_property($property);
            if (!empty($value) && !in_array($property, $hidden_properties))
            {
                $html[] = '<tr><td class="header">' . Translation::get(
                        (string) StringUtilities::getInstance()->createString($property)->upperCamelize()
                    ) . '</td><td>' . $value . '</td></tr>';
            }
        }

        $authors = $package_info->get_authors();
        foreach ($authors as $key => $author)
        {

            $html[] = '<tr><td class="header">';

            if ($key == 0)
            {
                $html[] = Translation::get('Authors');
            }

            $html[] = '</td><td>' .
                StringUtilities::getInstance()->encryptMailLink($author->get_email(), $author->get_name()) .
                '</td></tr>';
        }

        $html[] = '</table><br/>';

        return implode(PHP_EOL, $html);
    }

    public function get_registration()
    {
        return $this->get_application()->get_registration();
    }
}
