<?php
namespace Chamilo\Core\Install\Wizard;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use HTML_QuickForm_Page;

/**
 * $Id: install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * 
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * This abstract class defines a page which is used in a maintenance wizard.
 */
abstract class InstallWizardPage extends HTML_QuickForm_Page
{

    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * 
     * @param $name string A unique name of this page in the wizard
     * @param $parent Tool The repository tool in which the wizard runs.
     */
    public function __construct($name, $parent)
    {
        $this->parent = $parent;
        parent :: __construct($name, 'post');
        
        $element_folder = __DIR__ . '/../../../Libraries/Format/Form/Element/';
        
        $this->registerElementType(
            'style_submit_button', 
            $element_folder . 'HTML_QuickForm_stylesubmitbutton.php', 
            'HTML_QuickForm_stylesubmitbutton');
        $this->registerElementType(
            'style_reset_button', 
            $element_folder . 'HTML_QuickForm_styleresetbutton.php', 
            'HTML_QuickForm_styleresetbutton');
        $this->registerElementType(
            'category', 
            $element_folder . 'HTML_QuickForm_category.php', 
            'HTML_QuickForm_category');
    }

    /**
     * Returns the repository tool in which this wizard runs
     * 
     * @return Tool
     */
    public function get_parent()
    {
        return $this->parent;
    }

    public function set_lang($lang)
    {
        Translation :: set_language($lang);
    }

    public function get_breadcrumb()
    {
        return Translation :: get(ClassnameUtilities :: getInstance()->getClassnameFromObject($this));
    }

    public function get_title()
    {
        return Translation :: get(ClassnameUtilities :: getInstance()->getClassnameFromObject($this) . 'Title');
    }

    public function get_info()
    {
        return Translation :: get(ClassnameUtilities :: getInstance()->getClassnameFromObject($this) . 'Information');
    }
}
