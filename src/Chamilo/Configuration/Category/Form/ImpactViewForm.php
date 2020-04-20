<?php
namespace Chamilo\Configuration\Category\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * This form renders the impact view form with a confirm checkbox and a button
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImpactViewForm extends FormValidator
{
    const PROPERTY_ACCEPT_IMPACT = 'accept_impact';

    /**
     * Constructs this form
     */
    public function __construct($action)
    {
        parent::__construct('category_impact_view_form', self::FORM_METHOD_POST, $action);

        $this->addElement('checkbox', self::PROPERTY_ACCEPT_IMPACT, Translation::get('AcceptImpact'));
        $this->addRule(self::PROPERTY_ACCEPT_IMPACT, Translation::get('ThisFieldIsRequired'), 'required');

        $buttons[] = $this->createElement(
            'style_submit_button', 'delete', Translation::get('Delete'), array('class' => 'btn-danger'), null,
            new FontAwesomeGlyph('times')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
