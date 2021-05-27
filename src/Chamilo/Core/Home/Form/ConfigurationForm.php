<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Home\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConfigurationForm extends FormValidator
{
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    /**
     *
     * @var \Chamilo\Core\Home\Storage\DataClass\Block
     */
    private $block;

    /**
     *
     * @var boolean
     */
    private $hasStaticTitle;

    /**
     *
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param boolean $hasStaticTitle
     * @param string $action
     */
    public function __construct(Block $block, $hasStaticTitle)
    {
        parent::__construct('block', self::FORM_METHOD_POST, '');

        $this->block = $block;
        $this->hasStaticTitle = $hasStaticTitle;

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     *
     * @return \Chamilo\Core\Home\Storage\DataClass\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     *
     * @return boolean
     */
    public function getHasStaticTitle()
    {
        return $this->hasStaticTitle;
    }

    public function buildForm()
    {
        if (!$this->hasStaticTitle)
        {
            $this->addElement(
                'text', Block::PROPERTY_TITLE, Translation::get('Title'), array('class' => 'form-control')
            );
        }

        $this->addSettings();

        $this->addElement('hidden', Block::PROPERTY_ID, $this->getBlock()->get_id());

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Save', null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('save')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_submit_button', 'cancel', Translation::get('Cancel', null, Utilities::COMMON_LIBRARIES),
            array('class' => 'btn-danger'), null, new FontAwesomeGlyph('times')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    abstract function addSettings();

    /**
     *
     * @see HTML_QuickForm::setDefaults()
     */
    public function setDefaults($defaults = [])
    {
        if (!$this->hasStaticTitle)
        {
            $defaults[Block::PROPERTY_TITLE] = $this->getBlock()->getTitle();
        }

        parent::setDefaults($defaults);
    }
}
