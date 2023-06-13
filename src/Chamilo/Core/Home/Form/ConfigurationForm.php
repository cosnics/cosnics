<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Home\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigurationForm extends FormValidator
{
    public const RESULT_ERROR = 'ObjectUpdateFailed';
    public const RESULT_SUCCESS = 'ObjectUpdated';

    public function __construct()
    {
        parent::__construct('BlockRendererConfigurationForm');
    }

    /**
     * @throws \QuickformException
     */
    public function addSubmitButtons(Element $block): void
    {
        $translator = $this->getTranslator();

        $this->addElement('hidden', DataClass::PROPERTY_ID, $block->getId());

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $translator->trans('Save', [], StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('save')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_submit_button', 'cancel', $translator->trans('Cancel', [], StringUtilities::LIBRARIES),
            ['class' => 'btn-danger'], null, new FontAwesomeGlyph('times')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @throws \QuickformException
     */
    public function addTitleField(Element $block, bool $hasStaticTitle = true): void
    {
        $translator = $this->getTranslator();

        if (!$hasStaticTitle)
        {
            $this->addElement(
                'text', Element::PROPERTY_TITLE, $translator->trans('Title'), ['class' => 'form-control']
            );

            parent::setDefaults([Element::PROPERTY_TITLE => $block->getTitle()]);
        }
    }
}
