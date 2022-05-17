<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Activity\ActivityTable;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Component to list activity on a portfolio item
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityComponent extends BaseHtmlTreeComponent implements TableSupport, DelegateComponent
{

    /**
     * Executes this component
     */
    public function build()
    {
        $this->validateSelectedTreeNodeData();

        $activity_table = new ActivityTable($this);

        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId())),
                Translation::get('ActivityComponent')
            )
        );

        $html = [];

        $html[] = $this->render_header();
        $html[] = $activity_table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Backwards Compatibility for the generic ActivityTable
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function get_current_content_object()
    {
        return $this->getCurrentContentObject();
    }

    public function get_table_condition($table_class_name)
    {
    }
}
