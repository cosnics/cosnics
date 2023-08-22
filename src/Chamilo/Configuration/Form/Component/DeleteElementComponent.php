<?php
namespace Chamilo\Configuration\Form\Component;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package configuration\form
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleteElementComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $ids = $this->getRequest()->query->get(self::PARAM_DYNAMIC_FORM_ELEMENT_ID);

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        if (!is_array($ids))
        {
            $ids = [$ids];
        }

        if (count($ids) > 0)
        {
            $failures = 0;

            foreach ($ids as $id)
            {
                $dynamic_form_element = $this->getFormService()->retrieveDynamicFormElements(
                    new EqualityCondition(
                        new PropertyConditionVariable(Element::class, DataClass::PROPERTY_ID),
                        new StaticConditionVariable($id)
                    )
                )->current();

                if (!$dynamic_form_element->delete())
                {
                    $failures ++;
                }
            }

            $message = $this->get_result(
                $failures, count($ids), 'DynamicFormElementNotDeleted', 'DynamicFormElementsNotDeleted',
                'DynamicFormElementDeleted', 'DynamicFormElementsDeleted'
            );

            $this->redirectWithMessage(
                $message, ($failures > 0), [self::PARAM_ACTION => self::ACTION_BUILD_DYNAMIC_FORM]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities($this->getTranslator()->trans('NoObjectSelected', [], StringUtilities::LIBRARIES))
            );
        }
    }
}
