<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package user.lib.forms
 */
ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);

class UserExportForm extends FormValidator
{
    public const TYPE_EXPORT = 1;

    private $current_tag;

    private $current_value;

    private $user;

    private $users;

    /**
     * Creates a new UserImportForm Used to export users to a file
     */
    public function __construct($form_type, $action)
    {
        parent::__construct('user_export', self::FORM_METHOD_POST, $action, '_blank');

        $this->form_type = $form_type;
        $this->failedcsv = [];
        if ($this->form_type == self::TYPE_EXPORT)
        {
            $this->build_exporting_form();
        }
    }

    public function build_exporting_form()
    {
        $this->addElement(
            'select', 'file_type', Translation::get('OutputFileType'), ['Csv', 'Excel', 'Pdf', 'Xml']
        );

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Export', null, StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('download')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaults(['file_type' => 'csv']);
    }
}
