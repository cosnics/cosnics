<?php
namespace Chamilo\Core\Lynx\Manager;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

class PackageDisplay
{

    /**
     *
     * @var core\lynx\package\Package
     */
    private $package_info;

    private $application;

    public function __construct($application)
    {
        $this->application = $application;
        $this->package_info = Package :: get($application->get_context());
    }

    public function get_context()
    {
        return $this->get_application()->get_context();
    }

    public function get_registration()
    {
        return $this->get_application()->get_registration();
    }

    public function get_application()
    {
        return $this->application;
    }

    /**
     *
     * @return \core\lynx\package\Package
     */
    public function get_package_info()
    {
        return $this->package_info;
    }

    public function render()
    {
        $html = array();

        $html[] = $this->get_properties_table();
        $html[] = $this->get_dependencies_table();

        if (! $this->get_registration() instanceof Registration)
        {
            $html[] = $this->get_install_problems();
        }

        return implode(PHP_EOL, $html);
    }

    public function get_dependencies_table()
    {
        $package_info = $this->get_package_info();

        $html = array();

        if ($package_info->has_dependencies())
        {
            $html[] = '<h3>' . Translation :: get('Dependencies') . '</h3>';

            $html[] = '<table class="table table-striped table-bordered table-hover table-properties">';

            if (! is_null($package_info->get_pre_depends()))
            {
                $html[] = '<tr>';
                $html[] = '<th>' . Translation :: get('PreDepends') . '</th>';
                $html[] = '<td>' . $package_info->get_pre_depends()->as_html() . '</td>';
                $html[] = '</tr>';
            }

            if (! is_null($package_info->get_depends()))
            {
                $html[] = '<tr>';
                $html[] = '<th>' . Translation :: get('Depends') . '</th>';
                $html[] = '<td>' . $package_info->get_depends()->as_html() . '</td>';
                $html[] = '</tr>';
            }

            if (! is_null($package_info->get_recommends()))
            {
                $html[] = '<tr>';
                $html[] = '<th>' . Translation :: get('Recommends') . '</th>';
                $html[] = '<td>' . $package_info->get_recommends()->as_html() . '</td>';
                $html[] = '</tr>';
            }

            if (! is_null($package_info->get_suggests()))
            {
                $html[] = '<tr>';
                $html[] = '<th>' . Translation :: get('Suggests') . '</th>';
                $html[] = '<td>' . $package_info->get_suggests()->as_html() . '</td>';
                $html[] = '</tr>';
            }

            if (! is_null($package_info->get_enhances()))
            {
                $html[] = '<tr>';
                $html[] = '<th>' . Translation :: get('Enhances') . '</th>';
                $html[] = '<td>' . $package_info->get_enhances()->as_html() . '</td>';
                $html[] = '</tr>';
            }

            $html[] = '</table><br/>';
        }

        return implode(PHP_EOL, $html);
    }

    public function get_update_problems()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Package :: class_name(), Package :: PROPERTY_CONTEXT),
            new StaticConditionVariable($this->get_registration()->get_context()));

        $admin = \Chamilo\Core\Admin\Storage\DataManager :: get_instance();
        $order_by = new OrderBy(new PropertyConditionVariable(Package :: class_name(), Package :: PROPERTY_VERSION));

        $package_remote = $admin->retrieve_remote_packages($condition, $order_by, null, 1);

        if ($package_remote->size() == 1)
        {
            $package_remote = $package_remote->next_result();

            $package_update_dependency = new DependencyVerifier($package_remote);
            $success = $package_update_dependency->is_updatable();

            if ($success)
            {
                $type = 'Finished';
            }
            else
            {
                $type = 'Failed';
            }

            $html = array();

            $html[] = '<h3>' . Translation :: get('UpdateDependencies', array('VERSION' => $package_remote->get_version())) . '</h3>';

            $html[] = '<div class="panel panel-default">';

            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">';
            $html[] = Translation :: get('DependenciesResultVerification');
            $html[] = '</h3>';
            $html[] = '</div>';

            $html[] = '<div class="panel-body">';
            $html[] = '<img src="' . Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Place/' . $type) .
                 '" class="pull-left" />';
            $html[] = $package_update_dependency->get_logger()->render();
            $html[] = '</div>';

            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }
    }

    public function get_install_problems()
    {
        $package_dependency = new DependencyVerifier($this->get_package_info());
        $success = $package_dependency->is_installable();

        if ($success)
        {
            $type = 'Finished';
        }
        else
        {
            $type = 'Failed';
        }

        $html = array();

        $html[] = '<h3>' . Translation :: get('InstallationDependencies', array('VERSION' => $this->get_package_info()->get_version())) . '</h3>';

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = Translation :: get('DependenciesResultVerification');
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = '<img src="' . Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Place/' . $type) .
             '" class="pull-left" />';
        $html[] = $package_dependency->get_logger()->render();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function get_properties_table()
    {
        $package_info = $this->get_package_info();

        $html = array();

        $html[] = '<table class="table table-striped table-bordered table-hover table-properties">';

        $properties = $package_info->get_default_property_names();

        $hidden_properties = array(
            Package :: PROPERTY_AUTHORS,
            Package :: PROPERTY_VERSION,
            Package :: PROPERTY_PRE_DEPENDS,
            Package :: PROPERTY_DEPENDS,
            Package :: PROPERTY_RECOMMENDS,
            Package :: PROPERTY_SUGGESTS,
            Package :: PROPERTY_ENHANCES,
            Package :: PROPERTY_EXTRA);

        foreach ($properties as $property)
        {
            $value = $package_info->get_default_property($property);

            if (! empty($value) && ! in_array($property, $hidden_properties))
            {
                $html[] = '<tr>';
                $html[] = '<th>' . Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($property)->upperCamelize()) . '</th>';
                $html[] = '<td>' . $value . '</td>';
                $html[] = '</tr>';
            }
        }

        $authors = $package_info->get_authors();

        foreach ($authors as $key => $author)
        {

            $html[] = '<tr>';
            $html[] = '<th>';

            if ($key == 0)
            {
                $html[] = Translation :: get('Authors');
            }

            $html[] = '</th>';
            $html[] = '<td>' . StringUtilities :: getInstance()->encryptMailLink($author['email'], $author['name']) .
                 ' - ' . $author['company'] . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }
}
