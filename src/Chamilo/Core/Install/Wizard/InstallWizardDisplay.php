<?php
namespace Chamilo\Core\Install\Wizard;

use Chamilo\Core\Install\Wizard\Page\LanguagePage;
use Chamilo\Core\Install\Wizard\Page\PreconfiguredPage;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_QuickForm;
use HTML_QuickForm_Action_Display;

/**
 * $Id: install_wizard_display.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * This class provides the needed functionality to show a page in a maintenance wizard.
 */
class InstallWizardDisplay extends HTML_QuickForm_Action_Display
{

    /**
     *
     * @var \Chamilo\Core\Install\Manager
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

        $form_template = <<<EOT

<form {attributes}>
{content}
	<div class="clear">&nbsp;</div>
</form>

EOT;
        $renderer->setFormTemplate($form_template);

        $current_page->setRequiredNote(
            '<font color="#FF0000"><img src="' . Theme :: getInstance()->getCommonImagePath('Action/Required') .
                 '" alt="*" title ="*"/></font> ' .
                 Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES));
        // $element_template = "\n\t<tr>\n\t\t<td valign=\"top\"><!-- BEGIN required --><span style=\"color:
        // #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error
        // --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error
        // -->\t{element}</td>\n\t</tr>";
        $element_template = array();
        $element_template[] = '<div class="row">';
        $element_template[] = '<div class="label">';
        $element_template[] = '{label}<!-- BEGIN required --><span class="form_required"><img src="' .
             Theme :: getInstance()->getCommonImagePath('Action/Required') .
             '" alt="*" title ="*"/></span> <!-- END required -->';
        $element_template[] = '</div>';
        $element_template[] = '<div class="formw">';
        $element_template[] = '<div class="element"><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '</div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);

        $renderer->setElementTemplate($element_template);
        // $header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
        $header_template = array();
        $header_template[] = '<div class="row">';
        $header_template[] = '<div class="form_header">{header}</div>';
        $header_template[] = '</div>';
        $header_template = implode(PHP_EOL, $header_template);

        $renderer->setHeaderTemplate($header_template);
        HTML_QuickForm :: setRequiredNote(
            '<span class="form_required"><img src=src="' . Theme :: getInstance()->getCommonImagePath('Action/Required') .
                 '" alt="*" title ="*"/>&nbsp;<small>' .
                 Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES) . '</small></span>');
        $required_note_template = <<<EOT
	<div class="row">
		<div class="label"></div>
		<div class="formw">{requiredNote}</div>
	</div>
EOT;
        $renderer->setRequiredNoteTemplate($required_note_template);

        $current_page->accept($renderer);
        $all_pages = $current_page->controller->_pages;

        $total_number_of_pages = count($all_pages) + 1;

        if ($current_page instanceof LanguagePage)
        {
            $total_number_of_pages = 1;
        }

        if ($current_page instanceof PreconfiguredPage)
        {
            $total_number_of_pages = 3;
        }

        $current_page_number = 0;
        $page_number = 0;

        foreach ($all_pages as $index => $page)
        {
            if ($current_page instanceof LanguagePage && ! $page instanceof LanguagePage)
            {
                continue;
            }

            if ($current_page instanceof PreconfiguredPage)
            {
                if (! $page instanceof PreconfiguredPage && ! $page instanceof LanguagePage)
                {
                    continue;
                }
            }

            if ($page instanceof PreconfiguredPage)
            {
                if (! $current_page instanceof PreconfiguredPage)
                {
                    continue;
                }
            }

            if ($page->get_title() || $page->get_info())
            {
                $page_number ++;
            }

            if ($page->get_breadcrumb() == $current_page->get_breadcrumb())
            {
                $current_page_number = $page_number;
            }
        }

        $page_number = 0;

        foreach ($all_pages as $index => $page)
        {
            if ($current_page instanceof LanguagePage && ! $page instanceof LanguagePage)
            {
                continue;
            }

            if ($current_page instanceof PreconfiguredPage)
            {
                if (! $page instanceof PreconfiguredPage && ! $page instanceof LanguagePage)
                {
                    continue;
                }
            }

            if ($page instanceof PreconfiguredPage)
            {
                if (! $current_page instanceof PreconfiguredPage)
                {
                    continue;
                }
            }

            if ($page->get_title() || $page->get_info())
            {
                $page_number ++;
            }

            if ($page_number <= $current_page_number)
            {
                if ($page->get_title() || $page->get_info())
                {
                    $name = $page_number . '.&nbsp;&nbsp;' . $page->get_breadcrumb();
                    BreadcrumbTrail :: get_instance()->add(new Breadcrumb(null, $name));
                }
            }
        }

        $html = array();

        $html[] = $this->parent->render_header();

        if ($current_page->get_title() || $current_page->get_info())
        {
            $html[] = '<div id="theForm" style="margin: 10px;">';
            $html[] = '<div id="select" class="row"><div class="formc formc_no_margin">';
            $html[] = '<b>' . Translation :: get(
                'CurrentSteps',
                array('CURRENT' => $current_page_number, 'TOTAL' => $total_number_of_pages),
                Utilities :: COMMON_LIBRARIES) . ' &ndash; ' . $current_page->get_title() . '</b><br />';

            $html[] = $current_page->get_info();
            $html[] = '</div>';
            $html[] = '</div>';
        }

        if (isset($_SESSION['install_message']))
        {
            $html[] = Display :: normal_message($_SESSION['install_message']);
            unset($_SESSION['install_message']);
        }

        if (isset($_SESSION['install_error_message']))
        {
            $html[] = Display :: error_message($_SESSION['install_error_message']);
            unset($_SESSION['install_error_message']);
        }

        $html[] = $current_page->toHtml();
        $html[] = '</div>';

        $html[] = $this->parent->render_footer();

        return implode(PHP_EOL, $html);
    }
}
