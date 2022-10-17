<?php
namespace Chamilo\Libraries\Format\Form\Rule;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use HTML_QuickForm_Rule;

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
     * @param string $value   Wanted username
     * @param string $options The current username
     *
     * @return bool True if username is available
     * @throws \Exception
     */
    public function validate($value, $options = null): bool
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), new StaticConditionVariable($value)
        );

        if (!is_null($options))
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new StaticConditionVariable($options)
                )
            );
        }

        $condition = new AndCondition($conditions);
        $count = DataManager::count(
            User::class, new DataClassCountParameters($condition)
        );

        return $count == 0;
    }
}
