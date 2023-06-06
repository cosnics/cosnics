<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home;

use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Connector
{
    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
    }

    public function get_rss_feed_objects()
    {
        $options = [];

        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_ID))
        );

        $objects = DataManager::retrieve_active_content_objects(
            RssFeed::class, $condition
        );

        if ($objects->count() == 0)
        {
            $options[0] = Translation::get('CreateRssFeedFirst');
        }
        else
        {
            $options[0] = Translation::get('SelectRssFeed');
            foreach ($objects as $object)
            {
                $options[$object->get_id()] = $object->get_title();
            }
        }

        return $options;
    }
}
