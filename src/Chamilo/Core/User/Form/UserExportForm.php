<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package user.lib.forms
 */
ini_set("max_execution_time", - 1);
ini_set("memory_limit", - 1);
class UserExportForm extends FormValidator
{
    const TYPE_EXPORT = 1;

    private $current_tag;

    private $current_value;

    private $user;

    private $users;

    /**
     * Creates a new UserImportForm Used to export users to a file
     */
    public function __construct($form_type, $action)
    {
        parent::__construct('user_export', 'post', $action, '_blank');

        $this->form_type = $form_type;
        $this->failedcsv = array();
        if ($this->form_type == self::TYPE_EXPORT)
        {
            $this->build_exporting_form();
        }
    }

    public function build_exporting_form()
    {
        $this->addElement(
            'select',
            'file_type',
            Translation::get('OutputFileType'),
            Export::get_supported_filetypes(array('Pdf', 'Excel')));

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Export', null, Utilities::COMMON_LIBRARIES),
            null,
            null,
            'export');

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaults(array('file_type' => 'csv'));
    }
}
