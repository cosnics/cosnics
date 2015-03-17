<?php
namespace Chamilo\Core\Home\Action;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Storage\DataClass\BlockRegistration;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Extension of the generic installer for home blocks
 * 
 * @author Hans De Bisschop
 */
abstract class Remover extends \Chamilo\Configuration\Package\Action\Remover
{

    /**
     * Perform additional installation steps
     * 
     * @return boolean
     */
    public function extra()
    {
        if (! $this->deregister_home())
        {
            return $this->failed(Translation :: get('HomeFailed', null, Manager :: APPLICATION_NAME));
        }
        
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function deregister_home()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(BlockRegistration :: class_name(), BlockRegistration :: PROPERTY_CONTEXT), 
            new StaticConditionVariable(static :: context()));
        $registrations = DataManager :: retrieves(BlockRegistration :: class_name(), $condition);
        
        while ($registration = $registrations->next_result())
        {
            if ($registration->delete())
            {
                $this->add_message(
                    self :: TYPE_NORMAL, 
                    Translation :: get('DeregisteredBlock') . ': <em>' . $registration->get_block() . '</em>');
            }
            else
            {
                $this->add_message(
                    self :: TYPE_ERROR, 
                    Translation :: get('BlockDeregistrationFailed') . ': <em>' . $registration->get_block() . '</em>');
                return false;
            }
        }
        
        $this->add_message(self :: TYPE_NORMAL, Translation :: get('BlocksRemoved'));
        
        return true;
    }
}
