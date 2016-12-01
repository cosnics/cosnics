<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'action';
    const PARAM_TEMPLATE_ID = 'template_id';
    const PARAM_TEMPLATE_FUNCTION_PARAMETERS = 'template_function_parameters';
    const ACTION_BROWSE = 'Browser';
    const ACTION_VIEW = 'Viewer';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
    
    // Url Creation
    function get_viewer_url($template_id)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_VIEW, self::PARAM_TEMPLATE_ID => $template_id), 
            array(self::ACTION_BROWSE));
    }

    function get_browse_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}