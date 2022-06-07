<?php
namespace Chamilo\Core\Group\Form;

use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
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
        parent::__construct('group_export', self::FORM_METHOD_POST, $action, '_blank');

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
            'select', 'file_type', Translation::get('OutputFileType', null, StringUtilities::LIBRARIES),
            Export::get_supported_filetypes(array('Ical', 'Csv', 'Pdf', 'Excel'))
        );

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Export', null, StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('download')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->setDefaults(array('file_type' => 'xml'));
    }
}
