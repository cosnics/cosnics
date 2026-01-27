<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_category;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_styleresetbutton;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_stylesubmitbutton;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
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
            'message', $translator->trans('EmailMessage', [], Manager::CONTEXT), true, ['height' => 500, 'width' => 750]
        );

        $buttons[] = $this->createElement(
            HTML_QuickForm_stylesubmitbutton::class, 'submit', $translator->trans('Email', [], Manager::CONTEXT), null,
            null, new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            HTML_QuickForm_styleresetbutton::class, 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
