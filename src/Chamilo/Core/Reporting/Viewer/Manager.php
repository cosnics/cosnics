<?php
namespace Chamilo\Core\Reporting\Viewer;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * @author  Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
abstract class Manager extends Application
{
    public const ACTION_SAVE = 'Saver';
    public const ACTION_VIEW = 'Viewer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_VIEW;

    public const PARAM_ACTION = 'reporting_viewer_action';
    public const PARAM_BLOCK_ID = 'block_id';
    public const PARAM_FORMAT = 'format';
    public const PARAM_SHOW_ALL = 'show_all';
    public const PARAM_VIEW = 'view';
    public const PARAM_VIEWS = 'views';

    /**
     * @var \core\reporting\ReportingTemplate
     */
    private $template;

    /**
     * @return int
     */
    public function get_current_block()
    {
        return Request::get(self::PARAM_BLOCK_ID);
    }

    /**
     * @return string[]
     */
    public function get_current_view()
    {
        return Request::get(self::PARAM_VIEWS);
    }

    /**
     * @return \core\reporting\ReportingTemplate
     */
    public function get_template()
    {
        return $this->template;
    }

    /**
     * @param \core\reporting\ReportingTemplate $template
     */
    public function set_template(ReportingTemplate $template)
    {
        $this->template = $template;
    }

    /**
     * @param string $template_name
     */
    public function set_template_by_name($template_name)
    {
        $this->template = new $template_name($this->get_parent());
    }

    /**
     * @return bool
     */
    public function show_all()
    {
        $show_all = Request::get(self::PARAM_SHOW_ALL);

        return $show_all == 1;
    }
}
