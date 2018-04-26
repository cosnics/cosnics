<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ResultRenderer;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultViewerComponent extends Manager
{
    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        $this->validateAccess();

        $this->xslt_path = realpath(__DIR__ . '/../Resources/Xslt');

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();

        $requests = $this->getEphorusRequestsFromRequest();
        $request = $requests[0];

        $result_to_html_converter = new ResultRenderer($this->getRequestManager());

        $html[] = $result_to_html_converter->convert_to_html($request->getId());
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the actionbar
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get(
                        'PrintReport',
                        array(),
                        ClassnameUtilities::getInstance()->getNamespaceFromClassname(self::class)
                    ),
                    Theme::getInstance()->getCommonImagePath('Action/Item'),
                    '#',
                    ToolbarItem::DISPLAY_ICON_AND_LABEL,
                    false,
                    'print_button'
                )
            );

            $commonActions->addButton(
                new Button(
                    Translation::get(
                        'ExportReport',
                        array(),
                        ClassnameUtilities::getInstance()->getNamespaceFromClassname(self::class)
                    ),
                    Theme::getInstance()->getCommonImagePath('Action/Export'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_EXPORT_RESULT)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL,
                    false
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @return array|string[]
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_REQUEST_IDS);
    }
}