<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\Selector\Option\LinkTypeSelectorOption;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.complex_builder.assessment.component
 */
class BrowserComponent extends Manager
{

    public function run()
    {
        $this->get_complex_content_object_breadcrumbs();

        if ($this->get_complex_content_object_item())
        {
            $content_object = $this->get_complex_content_object_item()->get_ref_object();
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }
        $html = [];

        $html[] = $this->render_header();

        $buttonToolbarRenderer = $this->getButtonToolbarRenderer($this->get_root_content_object());

        if ($buttonToolbarRenderer)
        {
            $html[] = '<br />';
            $html[] = $buttonToolbarRenderer->render();
        }

        $html[] = ContentObjectRenditionImplementation::launch(
            $this->get_root_content_object(), ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL,
            $this
        );

        $html[] = $this->get_creation_links($content_object);
        $html[] = '<div class="clearfix"></div>';

        $html[] = '<div>';
        $html[] = $this->get_complex_content_object_table_html();
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the actionbar
     *
     * @param ContentObject $content_object
     *
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer($content_object = null)
    {
        $buttonToolbarRenderer = parent::getButtonToolbarRenderer($content_object);

        if (!$buttonToolbarRenderer instanceof ButtonToolBarRenderer)
        {
            $buttonToolbar = new ButtonToolBar();
        }
        else
        {
            $buttonToolbar = $buttonToolbarRenderer->getButtonToolBar();
        }

        $commonActions = new ButtonGroup();

        $preview_url = \Chamilo\Core\Repository\Manager::get_preview_content_object_url($content_object);

        $onclick = '" onclick="javascript:openPopup(\'' . addslashes($preview_url) . '\'); return false;';
        $commonActions->addButton(
            new Button(
                Translation::get('Preview', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('desktop'),
                $this->get_parent()->get_preview_content_object_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL, null,
                [$onclick], '_blank'
            )
        );

        $buttonToolbar->addButtonGroup($commonActions);

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /*
     * (non-PHPdoc) @see \core\repository\builder\Manager::get_additional_links()
     */
    public function get_additional_links()
    {
        $links = [];

        $links[] = new LinkTypeSelectorOption(
            self::package(), 'MergeAssessment', $this->get_url(
            [Manager::PARAM_ACTION => self::ACTION_MERGE_ASSESSMENT]
        ), new FontAwesomeGlyph('object-ungroup', ['fas-ci-va', 'fa-2x', 'fa-fw'], null, 'fas')
        );

        $links[] = new LinkTypeSelectorOption(
            self::package(), 'SelectQuestions', $this->get_url(
            [
                Manager::PARAM_ACTION => self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER
            ]
        ), new FontAwesomeGlyph('mouse', ['fas-ci-va', 'fa-2x', 'fa-fw'], null, 'fas')
        );

        $links[] = new LinkTypeSelectorOption(
            self::package(), 'RandomizeQuestionOptions',
            $this->get_url([Manager::PARAM_ACTION => self::ACTION_RANDOMIZE]),
            new FontAwesomeGlyph('random', ['fas-ci-va', 'fa-2x', 'fa-fw'], null, 'fas')
        );

        $links[] = new LinkTypeSelectorOption(
            self::package(), 'AnswerFeedbackType', $this->get_url(
            [Manager::PARAM_ACTION => self::ACTION_ANSWER_FEEDBACK_TYPE]
        ), new FontAwesomeGlyph('comments', ['fas-ci-va', 'fa-2x', 'fa-fw'], null, 'fas')
        );

        return $links;
    }
}
