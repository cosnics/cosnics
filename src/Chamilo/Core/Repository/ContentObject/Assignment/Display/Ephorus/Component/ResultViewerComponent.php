<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultViewerComponent extends Manager
{
    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();

        $entryId = $this->getRequest()->getFromPostOrUrl(self::PARAM_ENTRY_ID);
        $requests = $this->getDataProvider()->findEphorusRequestsForAssignmentEntries([$entryId]);

        if (empty($requests))
        {
            throw new UserException(
                Translation::getInstance()->getTranslation('RequestNotFound', null, self::EPHORUS_TRANSLATION_CONTEXT)
            );
        }

        $request = $requests[0];

        $html[] = $this->getReportRenderer()->renderRequestReport($request);
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
                        'PrintReport', [], self::EPHORUS_TRANSLATION_CONTEXT
                    ), new FontAwesomeGlyph('item'), '#', ToolbarItem::DISPLAY_ICON_AND_LABEL, false, 'print_button'
                )
            );

            $commonActions->addButton(
                new Button(
                    Translation::get(
                        'ExportReport', [], self::EPHORUS_TRANSLATION_CONTEXT
                    ), new FontAwesomeGlyph('download'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_EXPORT_RESULT)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL, false
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
        return [self::PARAM_ENTRY_ID];
    }
}