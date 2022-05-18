<?php
namespace Chamilo\Application\Portfolio\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Portfolio\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Publication extends DataClass
{

    // DataClass properties
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_MODIFIED = 'modified';

    /**
     *
     * @var \repository\ContentObject
     */
    private $content_object;

    /**
     *
     * @var \user\User
     */
    private $publisher;

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_CONTENT_OBJECT_ID,
                self::PROPERTY_PUBLISHER_ID,
                self::PROPERTY_PUBLISHED,
                self::PROPERTY_MODIFIED));
    }

    /**
     *
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @return ContentObject
     */
    public function get_content_object()
    {
        if (! isset($this->content_object))
        {
            $this->content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class,
                $this->get_content_object_id());
        }

        return $this->content_object;
    }

    /**
     *
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     *
     * @return int
     */
    public function get_publisher_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHER_ID);
    }

    /**
     *
     * @return \user\User
     */
    public function get_publisher()
    {
        if (! isset($this->publisher))
        {
            $this->publisher = DataManager::retrieve_by_id(
                User::class,
                $this->get_publisher_id());
        }

        return $this->publisher;
    }

    /**
     *
     * @param int $publisher_id
     */
    public function set_publisher_id($publisher_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    /**
     *
     * @return int
     */
    public function get_published()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHED);
    }

    /**
     *
     * @param int $published
     */
    public function set_published($published)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHED, $published);
    }

    /**
     *
     * @return int
     */
    public function get_modified()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIED);
    }

    /**
     *
     * @param int $modified
     */
    public function set_modified($modified)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFIED, $modified);
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    protected function getDependencies(array $dependencies = []): array
    {
        return array(
            Feedback::class => new EqualityCondition(
                new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($this->get_id())));
    }

    /**
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'portfolio_publication';
    }
}
