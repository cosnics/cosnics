<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Table\Entity;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableDataProvider
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return 10;
    }

    private function generateUsers()
    {
        $users = [];

        for ($i = 1; $i <= 10; $i ++)
        {
            $user = [];
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

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return new DataClassIterator(User::class, $this->generateUsers());
    }
}