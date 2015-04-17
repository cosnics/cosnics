<?php
namespace Chamilo\Core\Install\Wizard;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Form\FormValidatorPage;

/**
 * $Id: install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * This abstract class defines a page which is used in a maintenance wizard.
 */
abstract class InstallWizardPage extends FormValidatorPage
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
        $this->updateAttributes(array('action' => $parent->get_url()));
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
        Translation :: getInstance()->setLanguage($lang);
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
