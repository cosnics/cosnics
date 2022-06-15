<?php
namespace Chamilo\Core\Repository\Instance\Component;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Core\Repository\Instance\Form\InstanceForm;
use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class CreatorComponent extends Manager
{

    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $implementation = Request::get(self::PARAM_IMPLEMENTATION);

        if ($implementation && self::exists($implementation))
        {

            $form = new InstanceForm($this);

            if ($form->validate())
            {
                $success = $form->create_external_instance();
                $this->redirect(
                    Translation::get(
                        $success ? 'ObjectAdded' : 'ObjectNotAdded',
                        array('OBJECT' => Translation::get('ExternalInstance')), StringUtilities::LIBRARIES
                    ), !$success, array(self::PARAM_ACTION => self::ACTION_BROWSE)
                );
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $instance_types = $this->get_types();
            if (count($instance_types['sections']) == 0)
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $this->display_warning_message(Translation::get('NoExternalInstancesAvailable'));
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }

            foreach ($instance_types['sections'] as $category => $category_name)
            {
                $types_html = [];

                $types_html[] = '<div class="content-object-options">';
                $types_html[] = '<div id="' . $category . '" class="content-object-options-type">';
                $types_html[] = '<h4>' . $category_name . '</h4>';
                $types_html[] = '<ul class="list-group">';

                foreach ($instance_types['types'][$category] as $type => $registration)
                {
                    $glyph = new NamespaceIdentGlyph(
                        $registration->get_context(), true, false, false, IdentGlyph::SIZE_MEDIUM
                    );

                    $title = Translation::get('TypeName', null, $registration->get_context());

                    $types_html[] = '<li class="list-group-item">';

                    $types_html[] = $glyph->render();
                    $types_html[] = '&nbsp;&nbsp;';
                    $types_html[] = '<a href="' . $this->get_url(
                            array(self::PARAM_IMPLEMENTATION => $registration->get_context())
                        ) . '" title="' . htmlentities($title) . '">';
                    $types_html[] = $title;
                    $types_html[] = '</a>';
                    $types_html[] = '</li>';
                }

                $restOptions = (ceil(count($instance_types['types'][$category]) / 4) * 4) -
                    count($instance_types['types'][$category]);

                for ($i = 0; $i < $restOptions; $i ++)
                {
                    $types_html[] = '<li class="list-group-item"></li>';
                }

                $types_html[] = '</ul>';
                $types_html[] = '</div>';
                $types_html[] = '</div>';
            }

            $html = [];

            $html[] = $this->render_header();
            //$html[] = $tabs->render();
            $html[] = implode(PHP_EOL, $types_html);
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_implementation()
    {
        return Request::get(self::PARAM_IMPLEMENTATION);
    }

    public function get_types()
    {
        $active_managers = self::get_registered_types();
        $types = [];
        $sections = [];

        foreach ($active_managers as $active_manager)
        {
            $package_info = Package::get(
                $active_manager->get_context()
            );

            $section = $package_info->get_category() ? $package_info->get_category() : 'various';

            $extra = $package_info->get_extra();

            if ($extra['multiple'] == 1)
            {

                $multiple = true;
            }
            else
            {
                $multiple = false;
            }

            $condition = new EqualityCondition(
                new PropertyConditionVariable(Instance::class, Instance::PROPERTY_TYPE),
                new StaticConditionVariable($active_manager->get_context())
            );
            $parameters = new DataClassCountParameters($condition);
            $count = DataManager::count(Instance::class, $parameters);

            if (!$multiple && $count > 0)
            {
                continue;
            }

            if (!in_array($section, array_keys($sections)))
            {
                $sections[$section] = Translation::get(
                    (string) StringUtilities::getInstance()->createString($section)->upperCamelize(), null,
                    ClassnameUtilities::getInstance()->getNamespaceParent(
                        ClassnameUtilities::getInstance()->getNamespaceParent($package_info->get_context())
                    )
                );
            }

            if (!isset($types[$section]))
            {
                $types[$section] = [];
            }

            $types[$section][$active_manager->get_name()] = $active_manager;
            asort($types[$section]);
        }

        asort($sections);

        return array('sections' => $sections, 'types' => $types);
    }
}
