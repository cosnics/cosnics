<?php
namespace Chamilo\Core\Group\Form;

use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: group_export_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * 
 * @package groups.lib.forms
 */
class GroupExportForm extends FormValidator
{
    const TYPE_EXPORT = 1;

    private $current_tag;

    private $current_value;

    private $group;

    private $groups;

    /**
     * Creates a new GroupImportForm
     * Used to export groups to a file
     */
    public function __construct($form_type, $action)
    {
        parent :: __construct('group_export', 'post', $action, '_blank');
        
        $this->form_type = $form_type;
        $this->failedcsv = array();
        if ($this->form_type == self :: TYPE_EXPORT)
        {
            $this->build_exporting_form();
        }
    }

    public function build_exporting_form()
    {
        $this->addElement(
            'select', 
            'file_type', 
            Translation :: get('OutputFileType', null, Utilities :: COMMON_LIBRARIES), 
            Export :: get_supported_filetypes(array('ical', 'csv', 'pdf')));
        // $this->addElement('submit', 'group_export', Translation :: get('Ok', null , Utilities :: COMMON_LIBRARIES));
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Export', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive export'));
        // $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null , Utilities
        // :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->setDefaults(array('file_type' => 'xml'));
    }
}
