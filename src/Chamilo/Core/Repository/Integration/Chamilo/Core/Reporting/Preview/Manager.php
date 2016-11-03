<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Reporting\Preview;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'reporting_preview_action';

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_content_object()
    {
        return $this->get_parent()->get_content_object();
    }

    /**
     *
     * @return multitype:string
     */
    abstract static public function get_available_actions();
}