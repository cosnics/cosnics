<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Email\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EmailForm extends FormValidator
{

    /**
     * @throws \QuickformException
     */
    public function __construct(string $action)
    {
        parent::__construct('email_form', self::FORM_METHOD_POST, $action);

        $this->buildForm();
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('Email', [], Manager::CONTEXT));

        $this->addElement('text', 'title', $translator->trans('EmailTitle', [], Manager::CONTEXT), ['size' => '50']);
        $this->addRule('title', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required');

        $this->add_html_editor(
            'message', $translator->trans('EmailMessage', [], Manager::CONTEXT), true, ['height' => 500, 'width' => 750]
        );

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $translator->trans('Email', [], Manager::CONTEXT), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
