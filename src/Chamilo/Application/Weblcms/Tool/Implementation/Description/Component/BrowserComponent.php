<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Description\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class BrowserComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }

    /*
     * Inherited.
     */
    public function get_publication_count()
    {
        return count($this->publications);
    }

    /**
     * adds general info using hook function in other registered apps implement static function [application]Manager ::
     * weblcms_exclude_email_recipients($target_users) in any application to apply filtering from context
     *
     * @return string
     */
    function add_general_info()
    {
        // retrieve all applications
        $registrations = \Chamilo\Configuration\Storage\DataManager :: get_registrations();

        foreach ($registrations[\Chamilo\Configuration\Storage\DataManager :: REGISTRATION_TYPE] as $type)
        {
            foreach ($type as $registration)
            {
                if ($registration->is_active())
                {
                    // see if app has method implemented
                    $classname = $registration->get_context() . '\\' . $registration->get_name() . 'Manager';

                    if (class_exists($classname))
                    {
                        $method_name = 'weblcms_tool_description_add_general_info';
                        if (method_exists($classname, $method_name))
                        {
                            // filter out users
                            $html[] = $classname :: $method_name();
                        }
                    }
                }
            }
        }
        return count($html) > 0 ? '<div class="home_actions">' . implode(PHP_EOL, $html) . '</div>' : '';
    }

    public function render_header($visible_tools = null, $show_introduction_text = false)
    {
        $html = array();

        $html[] = parent :: render_header($visible_tools, $show_introduction_text);
        $html[] = $this->add_general_info();

        return implode(PHP_EOL, $html);
    }
}
