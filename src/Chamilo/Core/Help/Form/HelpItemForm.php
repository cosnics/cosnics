<?php
namespace Chamilo\Core\Help\Form;

use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Help\Form
 */
class HelpItemForm extends FormValidator
{

    private HelpItem $helpItem;

    /**
     * @throws \QuickformException
     */
    public function __construct(HelpItem $helpItem, $action)
    {
        parent::__construct('help_item', self::FORM_METHOD_POST, $action);

        $this->helpItem = $helpItem;
        $this->buildBasicForm();

        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function buildBasicForm(): void
    {
        $translator = $this->getTranslator();

        $this->addElement(
            'text', HelpItem::PROPERTY_URL, $translator->trans('URL', [], StringUtilities::LIBRARIES), ['size' => '100']
        );
        $this->addRule(
            HelpItem::PROPERTY_URL, $translator->trans('Required', [], StringUtilities::LIBRARIES), 'required'
        );

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $translator->trans('Save', [], StringUtilities::LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function getHelpItem(): HelpItem
    {
        return $this->helpItem;
    }

    public function setDefaults(array $defaultValues = [], $filter = null)
    {
        $defaults[HelpItem::PROPERTY_URL] = $this->getHelpItem()->get_url();

        parent::setDefaults($defaults);
    }
}
