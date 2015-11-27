<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * User: Pieterjan Broekaert Date: 30/07/12 Time: 12:41
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class ResultViewerComponent extends Manager
{

    public function run()
    {
        if ($this->can_execute_component())
        {
            $this->xslt_path = realpath(__DIR__ . '/../../../../resources/xslt');

            $actionbar = $this->get_action_bar();
            $html = array();

            $html[] = $this->render_header();
            $html[] = $actionbar->as_html();

            $request_id = \Chamilo\Libraries\Platform\Session\Request :: get(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager :: PARAM_CONTENT_OBJECT_IDS);
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager :: PARAM_CONTENT_OBJECT_IDS,
                $request_id);

            $result_to_html_converter = new ResultToHtmlConverter();

            $html[] = $result_to_html_converter->convert_to_html($request_id);
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    protected function can_execute_component()
    {
        return $this->get_parent()->is_allowed(WeblcmsRights :: EDIT_RIGHT);
    }

    /**
     * Returns the actionbar
     *
     * @return ActionBarRenderer
     */
    protected function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get(
                    'PrintReport',
                    array(),
                    ClassnameUtilities :: getInstance()->getNamespaceFromClassname(self :: class_name())),
                Theme :: getInstance()->getCommonImagePath('Action/Item'),
                '#',
                ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                false,
                'print_button'));

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get(
                    'ExportReport',
                    array(),
                    ClassnameUtilities :: getInstance()->getNamespaceFromClassname(self :: class_name())),
                Theme :: getInstance()->getCommonImagePath('Action/Export'),
                $this->get_url(array(Manager :: PARAM_ACTION => self :: ACTION_EXPORT_RESULT)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                false));

        return $action_bar;
    }
}