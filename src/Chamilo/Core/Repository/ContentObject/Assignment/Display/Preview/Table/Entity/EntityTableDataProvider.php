<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Table\Entity;

use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableDataProvider extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableDataProvider
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return new ArrayResultSet($this->generateUsers());
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        return 10;
    }

    private function generateUsers()
    {
        $users = array();
        
        for ($i = 1; $i <= 10; $i ++)
        {
            $user = array();
            $user[EntityTableColumnModel::PROPERTY_NAME] = 'Preview User ' . $i;
            // $user = new User();
            // $user->set_lastname('User');
            // $user->set_firstname('Test ' . $i);
            // $user->set_email('test.' . $i . '@user.com');
            // $user->set_username('test.' . $i . '@user.com');
            
            $users[] = $user;
        }
        
        return $users;
    }
}