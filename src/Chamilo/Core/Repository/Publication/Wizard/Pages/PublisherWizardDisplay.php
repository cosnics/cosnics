<?php
namespace Chamilo\Core\Repository\Publication\Wizard\Pages;

use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_QuickForm;
use HTML_QuickForm_Action_Display;

/**
 * $Id: publisher_wizard_display.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component.publication_wizard.pages
 */
/**
 * This class provides the needed functionality to show a page in a maintenance wizard.
 */
class PublisherWizardDisplay extends HTML_QuickForm_Action_Display
{

    /**
     * The repository tool in which the wizard runs
     */
    private $parent;

    /**
     * Constructor
     *
     * @param Tool $parent The repository tool in which the wizard runs
     */
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
        $renderer = $current_page->defaultRenderer();
        $current_page->setRequiredNote(
            '<font color="#FF0000">*</font> ' .
                 Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES));
        $header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
        $renderer->setHeaderTemplate($header_template);
        HTML_QuickForm :: setRequiredNote(
            '<font color="red">*</font> <small>' .
                 Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES) . '</small>');

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(
            new Breadcrumb($this->parent->get_url(), Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES)));

        $html = array();

        $html[] = $this->parent->render_header();
        $html[] = $current_page->toHtml();
        $html[] = $this->parent->render_footer();

        return implode("\n", $html);
    }
}
