<?php
namespace Chamilo\Configuration\Form\Component;

use Chamilo\Configuration\Form\Form\BuilderForm;
use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Configuration\Form\Component
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdateElementComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     */
    public function run()
    {
        $element_id = $this->getRequest()->query->get(self::PARAM_DYNAMIC_FORM_ELEMENT_ID);
        $parameters = [self::PARAM_DYNAMIC_FORM_ELEMENT_ID => $element_id];
        $translator = $this->getTranslator();

        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb($this->get_url($parameters), $translator->trans('UpdateElement', [], Manager::CONTEXT))
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, DataClass::PROPERTY_ID),
            new StaticConditionVariable($element_id)
        );
        $element = $this->getFormService()->retrieveDynamicFormElements($condition)->current();

        $form = new BuilderForm(BuilderForm::TYPE_EDIT, $element, $this->get_url($parameters), $this->getUser());

        if ($form->validate())
        {
            $success = $form->update_dynamic_form_element();
            $this->redirectWithMessage(
                $translator->trans($success ? 'DynamicFormElementUpdated' : 'DynamicFormElementNotUpdated', [],
                    Manager::CONTEXT), !$success, [self::PARAM_ACTION => self::ACTION_BUILD_DYNAMIC_FORM]
            );
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }
}
