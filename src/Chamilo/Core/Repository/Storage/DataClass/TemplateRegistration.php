<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Common\Template\Template;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * The registration of a template for a specific content object type
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TemplateRegistration extends DataClass
{

    // Default properties
    const PROPERTY_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CREATOR_ID = 'creator_id';
    const PROPERTY_DEFAULT = 'is_default';
    const PROPERTY_TEMPLATE = 'template';

    /**
     *
     * @var Template
     */
    private $template;

    /**
     *
     * @param string $content_object_type
     */
    public function set_content_object_type($content_object_type)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE, $content_object_type);
    }

    /**
     *
     * @return string
     */
    public function get_content_object_type()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE);
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     *
     * @return \core\user\User
     */
    public function get_user()
    {
        if (! isset($this->user))
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                $this->get_user_id());
        }

        return $this->user;
    }

    /**
     *
     * @param int $creator_id
     */
    public function set_creator_id($creator_id)
    {
        $this->set_default_property(self :: PROPERTY_CREATOR_ID, $creator_id);
    }

    /**
     *
     * @return int
     */
    public function get_creator_id()
    {
        return $this->get_default_property(self :: PROPERTY_CREATOR_ID);
    }

    /**
     *
     * @return \core\user\User
     */
    public function get_creator()
    {
        if (! isset($this->creator))
        {
            $this->creator = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                $this->get_creator_id());
        }

        return $this->creator;
    }

    /**
     *
     * @param boolean $default
     */
    public function set_default($default)
    {
        $this->set_default_property(self :: PROPERTY_DEFAULT, $default);
    }

    /**
     *
     * @return boolean
     */
    public function get_default()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT);
    }

    /**
     *
     * @return Template
     */
    public function get_template()
    {
        return unserialize($this->get_default_property(self :: PROPERTY_TEMPLATE));
    }

    /**
     *
     * @param Template $template
     */
    public function set_template($template)
    {
        $this->set_default_property(self :: PROPERTY_TEMPLATE, serialize($template));
    }

    /**
     *
     * @param multitype:string $extended_property_names
     * @return multitype:string
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_TYPE;
        $extended_property_names[] = self :: PROPERTY_NAME;
        $extended_property_names[] = self :: PROPERTY_USER_ID;
        $extended_property_names[] = self :: PROPERTY_CREATOR_ID;
        $extended_property_names[] = self :: PROPERTY_DEFAULT;
        $extended_property_names[] = self :: PROPERTY_TEMPLATE;

        return parent :: get_default_property_names($extended_property_names);
    }

    public function synchronize()
    {
        try
        {
            $this->set_template(Template :: get($this->get_content_object_type(), $this->get_name()));
            return $this->update();
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }
}