<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\User\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class XmlUserFeedComponent extends \Chamilo\Core\User\Ajax\Manager
{

    public function run()
    {
        $conditions = array();
        
        $query_condition = Utilities::query_to_condition(
            $_GET['query'], 
            array(User::PROPERTY_USERNAME, User::PROPERTY_FIRSTNAME, User::PROPERTY_LASTNAME));
        if (isset($query_condition))
        {
            $conditions[] = $query_condition;
        }
        
        if (is_array($_GET['exclude']))
        {
            $c = array();
            foreach ($_GET['exclude'] as $id)
            {
                $c[] = new EqualityCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), 
                    new StaticConditionVariable($id));
            }
            $conditions[] = new NotCondition(new OrCondition($c));
        }
        
        if (count($conditions) > 0)
        {
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = null;
        }
        
        $users = DataManager::retrieves(
            User::class_name(), 
            new DataClassRetrievesParameters(
                $condition, 
                null, 
                null, 
                array(
                    new OrderBy(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), 
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME)))));
        
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<tree>', "\n";
        
        if (isset($users))
        {
            $this->dump_tree($users);
        }
        
        echo '</tree>';
    }

    public function dump_tree($users)
    {
        if (isset($users) && $users->size() == 0)
        {
            return;
        }
        
        echo '<node id="0" classes="category unlinked" title="' . Translation::get('Users') . '">' . "\n";
        
        while ($user = $users->next_result())
        {
            echo '<leaf id="user_' . $user->get_id() . '" classes="type type_user" title="' .
                 htmlspecialchars($user->get_fullname()) . '" description="' . htmlspecialchars($user->get_username()) .
                 '"/>' . "\n";
        }
        
        echo '</node>' . "\n";
    }
}