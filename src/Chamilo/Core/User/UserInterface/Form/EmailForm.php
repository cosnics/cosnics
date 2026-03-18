<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_category;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_button_reset;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_button_submit;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_text;

/**
 * @package Chamilo\Core\User\Form
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

        $this->addElement(HTML_QuickForm_category::class, $translator->trans('Email', [], Manager::CONTEXT));

        $this->addElement(
            HTML_QuickForm_text::class, 'title', $translator->trans('EmailTitle', [], Manager::CONTEXT),
            ['size' => '50']
        );
        $this->addRule('title', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            HTML_QuickForm_Rule_Required::class);

        $this->addHtmlEditor(
            'message', $translator->trans('EmailMessage', [], Manager::CONTEXT)
        );

        $buttons[] = $this->createElement(
            HTML_QuickForm_button_submit::class, 'submit', $translator->trans('Email', [], Manager::CONTEXT), null,
            null, new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            HTML_QuickForm_button_reset::class, 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
