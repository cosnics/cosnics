<?php
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * QuickForm rule to check if a username is available
 *
 * @package Chamilo\Libraries\Format\Form\Rule
 */
class HTML_QuickForm_Rule_UsernameAvailable extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a username is available
     *
     * @param string $username Wanted username
     * @param string $currentUsername
     * @return boolean True if username is available
     */
    public function validate($username, $currentUsername = null)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
            new StaticConditionVariable($username));

        if (! is_null($currentUsername))
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new StaticConditionVariable($currentUsername)));
        }

        $condition = new AndCondition($conditions);
        $count = DataManager::count(
            User::class,
            new DataClassCountParameters($condition));

        return $count == 0;
    }
}
