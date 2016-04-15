<?php
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package common.html.formvalidator.Rule
 */
/**
 * QuickForm rule to check if a username is available
 */
class HTML_QuickForm_Rule_UsernameAvailable extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a username is available
     *
     * @see HTML_QuickForm_Rule
     * @param string $username Wanted username
     * @param string $current_username
     * @return boolean True if username is available
     */
    public function validate($username, $current_username = null)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME),
            new StaticConditionVariable($username));

        if (! is_null($current_username))
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME),
                    new StaticConditionVariable($current_username)));
        }

        $condition = new AndCondition($conditions);
        $count = \Chamilo\Core\User\Storage\DataManager :: count(\Chamilo\Core\User\Storage\DataClass\User :: class_name(), $condition);

        return $count == 0;
    }
}
