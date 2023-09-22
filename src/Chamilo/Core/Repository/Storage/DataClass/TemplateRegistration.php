<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Common\Template\Template;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Exception;

/**
 * The registration of a template for a specific content object type
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TemplateRegistration extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CONTENT_OBJECT_TYPE = 'content_object_type';
    public const PROPERTY_CREATOR_ID = 'creator_id';
    public const PROPERTY_DEFAULT = 'is_default';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_TEMPLATE = 'template';
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * @var Template
     */
    private $template;

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_CREATOR_ID;
        $extendedPropertyNames[] = self::PROPERTY_DEFAULT;
        $extendedPropertyNames[] = self::PROPERTY_TEMPLATE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_template_registration';
    }

    /**
     * @return string
     */
    public function get_content_object_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_TYPE);
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function get_creator()
    {
        if (!isset($this->creator))
        {
            $this->creator = DataManager::retrieve_by_id(
                User::class, $this->get_creator_id()
            );
        }

        return $this->creator;
    }

    /**
     * @return int
     */
    public function get_creator_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATOR_ID);
    }

    /**
     * @return bool
     */
    public function get_default()
    {
        return $this->getDefaultProperty(self::PROPERTY_DEFAULT);
    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * @return Template
     */
    public function get_template()
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_TEMPLATE));
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function get_user()
    {
        if (!isset($this->user))
        {
            $this->user = DataManager::retrieve_by_id(
                User::class, $this->get_user_id()
            );
        }

        return $this->user;
    }

    /**
     * @return int
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @param string $content_object_type
     *
     * @throws \Exception
     */
    public function set_content_object_type($content_object_type)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_TYPE, $content_object_type);
    }

    /**
     * @param int $creator_id
     *
     * @throws \Exception
     */
    public function set_creator_id($creator_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATOR_ID, $creator_id);
    }

    /**
     * @param bool $default
     *
     * @throws \Exception
     */
    public function set_default($default)
    {
        $this->setDefaultProperty(self::PROPERTY_DEFAULT, $default);
    }

    /**
     * @param string $name
     *
     * @throws \Exception
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * @param Template $template
     *
     * @throws \Exception
     */
    public function set_template($template)
    {
        $this->setDefaultProperty(self::PROPERTY_TEMPLATE, serialize($template));
    }

    /**
     * @param int $user_id
     *
     * @throws \Exception
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    public function synchronize()
    {
        try
        {
            $this->set_template(Template::get($this->get_content_object_type(), $this->get_name()));

            return $this->update();
        }
        catch (Exception $exception)
        {
            return false;
        }
    }
}