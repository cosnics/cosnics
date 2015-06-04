<?php
namespace Chamilo\Core\Tracking\Archive;

use Chamilo\Core\Tracking\Archive\Page\ConfirmationArchiveWizardPage;
use Chamilo\Core\Tracking\Archive\Page\SettingsArchiveWizardPage;
use Chamilo\Core\Tracking\Archive\Page\TrackersSelectionArchiveWizardPage;
use HTML_QuickForm_Controller;

/**
 * $Id: archive_wizard.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * 
 * @package tracking.lib.tracking_manager.component.wizards
 */
/**
 * A wizard which guides the user through several steps to perform the archive
 * 
 * @author Sven Vanpoucke
 */
class ArchiveWizard extends HTML_QuickForm_Controller
{

    /**
     * The component in which the wizard runs
     */
    private $parent;

    /**
     * Creates a new ArchiveWizard
     * 
     * @param $parent ArchiveComponent The archive component in which this wizard runs.
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        parent :: __construct('ArchiveWizard', true);
        $this->addPage(new TrackersSelectionArchiveWizardPage('page_trackers', $this->parent));
        $this->addPage(new SettingsArchiveWizardPage('page_settings', $this->parent));
        $this->addPage(new ConfirmationArchiveWizardPage('page_confirmation', $this->parent));
        
        $this->addAction('display', new ArchiveWizardDisplay($this->parent));
        $this->addAction('process', new ArchiveWizardProcess($this->parent));
    }
}
