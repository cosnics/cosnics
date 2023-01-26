<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Reporting\Preview;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{

    public const CONTEXT = __NAMESPACE__;
    public const PARAM_ACTION = 'reporting_preview_action';

    /**
     * @return string[]
     */
    abstract public static function get_available_actions();

    /**
     * @return \core\repository\ContentObject
     */
    public function get_content_object()
    {
        return $this->get_parent()->get_content_object();
    }
}