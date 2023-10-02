<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Form
 */
class UserImportForm extends FormValidator
{
    /**
     * @throws \QuickformException
     */
    public function __construct($action)
    {
        parent::__construct('user_import', self::FORM_METHOD_POST, $action);

        $this->buildForm();
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $translator = $this->getTranslator();

        $this->addElement('file', 'file', $translator->trans('FileName', [], Manager::CONTEXT));
        $allowed_upload_types = ['xml', 'csv'];
        $this->addRule(
            'file', $translator->trans('OnlyCSVAllowed', [], Manager::CONTEXT), 'filetype', $allowed_upload_types
        );

        $group = [];
        $group[] = $this->createElement(
            'radio', 'send_mail', null, $translator->trans('ConfirmYes', [], StringUtilities::LIBRARIES), 1
        );
        $group[] = $this->createElement(
            'radio', 'send_mail', null, $translator->trans('ConfirmNo', [], StringUtilities::LIBRARIES), 0
        );
        $this->addGroup($group, 'mail', $translator->trans('SendMailToNewUser', [], Manager::CONTEXT), '');

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $translator->trans('Import', [], StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('import')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $defaults['mail']['send_mail'] = 1;
        $this->setDefaults($defaults);
    }
}
