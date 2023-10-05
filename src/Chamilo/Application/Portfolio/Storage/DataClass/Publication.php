<?php
namespace Chamilo\Application\Portfolio\Storage\DataClass;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Portfolio\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Publication extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    public const PROPERTY_MODIFIED = 'modified';
    public const PROPERTY_PUBLISHED = 'published';
    public const PROPERTY_PUBLISHER_ID = 'publisher_id';

    /**
     * @var ContentObject
     */
    private $content_object;

    /**
     * @var User
     */
    private $publisher;

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_CONTENT_OBJECT_ID,
                self::PROPERTY_PUBLISHER_ID,
                self::PROPERTY_PUBLISHED,
                self::PROPERTY_MODIFIED
            ]
        );
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    protected function getDependencies(array $dependencies = []): array
    {
        return [
            Feedback::class => new EqualityCondition(
                new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($this->get_id())
            )
        ];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'portfolio_publication';
    }

    /**
     * @return ContentObject
     */
    public function get_content_object()
    {
        if (!isset($this->content_object))
        {
            $this->content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $this->get_content_object_id()
            );
        }

        return $this->content_object;
    }

    /**
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @return int
     */
    public function get_modified()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIED);
    }

    /**
     * @return int
     */
    public function get_published()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHED);
    }

    /**
     * @return \user\User
     */
    public function get_publisher()
    {
        if (!isset($this->publisher))
        {
            $this->publisher = DataManager::retrieve_by_id(
                User::class, $this->get_publisher_id()
            );
        }

        return $this->publisher;
    }

    /**
     * @return int
     */
    public function get_publisher_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHER_ID);
    }

    /**
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * @param int $modified
     */
    public function set_modified($modified)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFIED, $modified);
    }

    /**
     * @param int $published
     */
    public function set_published($published)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHED, $published);
    }

    /**
     * @param int $publisher_id
     */
    public function set_publisher_id($publisher_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHER_ID, $publisher_id);
    }
}
