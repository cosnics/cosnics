<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_VIEW = 'Viewer';

    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'action';
    public const PARAM_TEMPLATE_FUNCTION_PARAMETERS = 'template_function_parameters';
    public const PARAM_TEMPLATE_ID = 'template_id';

    // Url Creation

    public function get_browse_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }

    public function get_viewer_url($template_id)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_VIEW, self::PARAM_TEMPLATE_ID => $template_id),
            array(self::ACTION_BROWSE)
        );
    }
}