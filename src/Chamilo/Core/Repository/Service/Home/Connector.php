<?php
namespace Chamilo\Core\Repository\Service\Home;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package repository.block.connectors
 */

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 *
 * @author Hans De Bisschop
 */
class Connector
{

    public function getDisplayerObjects()
    {
        $objectTypes = [];

        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement';
        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description';
        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass\Note';
        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link';
        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataClass\Matterhorn';
        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Slideshare\Storage\DataClass\Slideshare';
        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Soundcloud\Storage\DataClass\Soundcloud';
        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo';
        $objectTypes[] = 'Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube';

        return self::get_objects($objectTypes);
    }

    /**
     * Returns a list of objects for the specified types.
     *
     * @param array $types
     *
     * @return array
     */
    public static function get_objects($types)
    {
        $session = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);

        $result = [];

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($session->get(\Chamilo\Core\User\Manager::SESSION_USER_ID))
        );

        $types_condition = [];
        foreach ($types as $type)
        {
            $types_condition[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
                new StaticConditionVariable($type)
            );
        }
        $conditions[] = new OrCondition($types_condition);
        $condition = new AndCondition($conditions);

        $objects = DataManager::retrieve_active_content_objects(
            ContentObject::class, $condition
        );

        if ($objects->count() == 0)
        {
            $result[0] = Translation::get('CreateObjectFirst');
        }
        else
        {
            foreach ($objects as $object)
            {
                $result[$object->get_id()] = $object->get_title();
            }
        }

        return $result;
    }
}
