<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard;

use HTML_QuickForm_Action_Display;

/**
 * $Id: assessment_viewer_wizard_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard
 */
/**
 *
 * @author Sven Vanpoucke
 */
class AssessmentViewerWizardDisplay extends HTML_QuickForm_Action_Display
{

    private $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     *
     * @param HTML_Quickform_Page $page The page to display.
     */
    public function _renderForm($current_page)
    {
        $html = array();

        $html[] = $this->parent->get_parent()->render_header();
        $html[] = '<div class="assessment">';
        $html[] = '<h2>' . $this->parent->get_assessment()->get_title() . '</h2>';

        if ($this->parent->get_assessment()->has_description() && $current_page->get_page_number() == 1)
        {
            $html[] = '<div class="description">';
            $html[] = $this->parent->get_assessment()->get_description();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        $html[] = '</div>';
        $html[] = '<div style="width: 100%; text-align: center;">';
        $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
        $html[] = '</div>';
        $html[] = '<div>';
        $html[] = $current_page->toHtml();
        $html[] = '</div>';
        $html[] = '<div style="width: 100%; text-align: center;">';
        $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
        $html[] = '</div>';
        $html[] = $this->parent->get_parent()->render_footer();

        return implode(PHP_EOL, $html);
    }
}
