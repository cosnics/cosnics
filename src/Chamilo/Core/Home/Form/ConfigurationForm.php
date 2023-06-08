<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Home\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConfigurationForm extends FormValidator
{
    public const RESULT_ERROR = 'ObjectUpdateFailed';

    public const RESULT_SUCCESS = 'ObjectUpdated';

    /**
     * @var \Chamilo\Core\Home\Storage\DataClass\Element
     */
    private $block;

    /**
     * @var bool
     */
    private $hasStaticTitle;

    /**
     * @param \Chamilo\Core\Home\Storage\DataClass\Element $block
     * @param bool $hasStaticTitle
     * @param string $action
     */
    public function __construct(Element $block, $hasStaticTitle)
    {
        parent::__construct('block', self::FORM_METHOD_POST);

        $this->block = $block;
        $this->hasStaticTitle = $hasStaticTitle;

        $this->buildForm();
        $this->setDefaults();
    }

    abstract public function addSettings();

    public function buildForm()
    {
        if (!$this->hasStaticTitle)
        {
            $this->addElement(
                'text', Element::PROPERTY_TITLE, Translation::get('Title'), ['class' => 'form-control']
            );
        }

        $this->addSettings();

        $this->addElement('hidden', Element::PROPERTY_ID, $this->getBlock()->get_id());

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Save', null, StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('save')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_submit_button', 'cancel', Translation::get('Cancel', null, StringUtilities::LIBRARIES),
            ['class' => 'btn-danger'], null, new FontAwesomeGlyph('times')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\Element
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @return bool
     */
    public function getHasStaticTitle()
    {
        return $this->hasStaticTitle;
    }

    /**
     * @see HTML_QuickForm::setDefaults()
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        if (!$this->hasStaticTitle)
        {
            $defaults[Element::PROPERTY_TITLE] = $this->getBlock()->getTitle();
        }

        parent::setDefaults($defaults);
    }
}
