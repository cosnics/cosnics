<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
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
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param string $action
     */
    public function __construct(Block $block)
    {
        parent :: __construct('block', 'post', '');

        $this->block = $block;
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

    public function buildForm()
    {
        $this->addSettings();

        $this->addElement('hidden', Block :: PROPERTY_ID, $this->getBlock()->get_id());

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'save');
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_submit_button',
            'cancel',
            Translation :: get('Cancel', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'btn-danger'),
            null,
            'remove');

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    abstract function addSettings();
}
