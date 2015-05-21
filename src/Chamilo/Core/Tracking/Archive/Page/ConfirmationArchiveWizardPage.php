<?php
namespace Chamilo\Core\Tracking\Archive\Page;

use Chamilo\Core\Tracking\Archive\ArchiveWizardPage;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: confirmation_archive_wizard_page.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * 
 * @package tracking.lib.tracking_manager.component.wizards.archive
 */
/**
 * Page in the archive wizard in which confirmation is ask for the given options to the user.
 */
class ConfirmationArchiveWizardPage extends ArchiveWizardPage
{

    /**
     * Returns the title of this page
     * 
     * @return string the title
     */
    public function get_title()
    {
        return Translation :: get('ArchiveConfirmationTitle');
    }

    /**
     * Returns the info of this page
     * 
     * @return string the info
     */
    public function get_info()
    {
        return Translation :: get('ArchiveConfirmationInfo');
    }

    /**
     * Builds the form that must be visible on this page
     */
    public function buildForm()
    {
        $this->_formBuilt = true;
        $exports = $this->controller->exportValues();
        
        $this->addElement(
            'html', 
            '<div style="margin-top: 10px;">' . Translation :: get('You_have_chosen_following_events_and_trackers') .
                 ':</div>');
        
        foreach ($exports as $key => $export)
        {
            if (substr($key, strlen($key) - strlen('event'), strlen($key)) == 'event')
            {
                $this->addElement(
                    'html', 
                    '<div style="left: 20px; position: relative; margin-top: 5px;">' . $key . '</div>');
                $eventname = substr($key, 0, strlen($key) - strlen('event'));
                
                foreach ($exports as $key2 => $export2)
                {
                    if ((strpos($key2, $eventname) !== false) && ($key2 != $key))
                    {
                        $id = substr($key2, strlen($eventname . 'event_'));
                        $tracker = $this->get_parent()->retrieve_tracker_registration($id);
                        $this->addElement(
                            'html', 
                            '<div style="margin-top: 3px; left: 40px; position: relative;">' . $tracker->get_class() .
                                 '</div>');
                    }
                }
            }
        }
        
        $startdate = $exports['start_date'];
        $enddate = $exports['end_date'];
        
        $period = $exports['period'];
        
        $this->addElement(
            'html', 
            '<div style="margin-top: 13px">' . Translation :: get('Start_date') . ': ' . $startdate . ' 00:00:00</div>');
        $this->addElement(
            'html', 
            '<div style="margin-top: 3px">' . Translation :: get('End_date') . ': ' . $enddate . ' 23:59:59</div>');
        $this->addElement(
            'html', 
            '<div style="margin-top: 3px">' . Translation :: get('Period') . ': ' . $period . ' ' . Translation :: get(
                'Days') . '</div>');
        
        $prevnext[] = $this->createElement(
            'style_submit_button', 
            $this->getButtonName('back'), 
            '<< ' . Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'previous'));
        $prevnext[] = $this->createElement(
            'style_submit_button', 
            $this->getButtonName('next'), 
            Translation :: get('Confirm', null, Utilities :: COMMON_LIBRARIES) . ' >>', 
            array('class' => 'positive finish'));
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
    }
}
