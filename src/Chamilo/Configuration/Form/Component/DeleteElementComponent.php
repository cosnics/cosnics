<?php
namespace Chamilo\Configuration\Form\Component;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleteElementComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request::get(self::PARAM_DYNAMIC_FORM_ELEMENT_ID);
        
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        if (! is_array($ids))
        {
            $ids = array($ids);
        }
        
        if (count($ids) > 0)
        {
            $failures = 0;
            
            foreach ($ids as $id)
            {
                $dynamic_form_element = DataManager::retrieve_dynamic_form_elements(
                    new EqualityCondition(
                        new PropertyConditionVariable(Element::class, Element::PROPERTY_ID),
                        new StaticConditionVariable($id)))->current();
                
                if (! $dynamic_form_element->delete())
                {
                    $failures ++;
                }
            }
            
            $message = $this->get_result(
                $failures, 
                count($ids), 
                'DynamicFormElementNotDeleted', 
                'DynamicFormElementsNotDeleted', 
                'DynamicFormElementDeleted', 
                'DynamicFormElementsDeleted');
            
            $this->redirect($message, ($failures > 0), array(self::PARAM_ACTION => self::ACTION_BUILD_DYNAMIC_FORM));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectSelected', null, Utilities::COMMON_LIBRARIES)));
        }
    }
}
