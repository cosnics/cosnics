<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Viewer for assignments
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class ViewerComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    /**
     * Adds toolbar items to the toolbar
     *
     * @return array ToolbarItems
     */
    public function get_tool_actions()
    {
        $actions = array();
        $publication_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);

        $actions[] = new Button(
            Translation::get('BrowseSubmitters'),
            new FontAwesomeGlyph('folder-open'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id
                )
            )
        );

        return $actions;
    }

    /**
     * @return string
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT)) {
            throw new NotAllowedException();
        }
        return parent::run();
    }
}
