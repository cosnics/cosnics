<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBarRenderer
     */
    private $actionBar;

    public function run()
    {
        $table = new WorkspaceTable($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->getActionBar()->as_html();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBarRenderer
     */
    public function getActionBar()
    {
        if (! isset($this->actionBar))
        {
            $this->actionBar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

            $this->actionBar->set_search_url($this->get_url());

            $this->actionBar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Create'),
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE)),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $this->actionBar;
    }
}