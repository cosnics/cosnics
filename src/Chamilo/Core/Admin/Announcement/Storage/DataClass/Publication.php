<?php
namespace Chamilo\Core\Admin\Announcement\Storage\DataClass;

use Chamilo\Core\Admin\Announcement\Rights;
use Chamilo\Core\Admin\Announcement\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package admin.lib $Id: system_announcement_publication.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @author Hans De Bisschop
 */
class Publication extends DataClass
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_PUBLICATION_DATE = 'published';
    const PROPERTY_MODIFICATION_DATE = 'modified';
    const PROPERTY_EMAIL_SENT = 'email_sent';

    /**
     *
     * @var \core\repository\ContentObject
     */
    private $content_object;

    /**
     *
     * @var \core\user\User
     */
    private $publisher;

    /**
     *
     * @var multitype:int
     */
    private $target_users;

    /**
     *
     * @var multitype:int
     */
    private $target_groups;

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_CONTENT_OBJECT_ID,
                self :: PROPERTY_FROM_DATE,
                self :: PROPERTY_TO_DATE,
                self :: PROPERTY_HIDDEN,
                self :: PROPERTY_PUBLISHER_ID,
                self :: PROPERTY_PUBLICATION_DATE,
                self :: PROPERTY_MODIFICATION_DATE,
                self :: PROPERTY_EMAIL_SENT));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: getInstance();
    }

    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    public function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    public function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    public function get_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    public function get_publisher_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER_ID);
    }

    /**
     * Gets the date on which this publication was made
     *
     * @return int
     */
    public function get_publication_date()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_DATE);
    }

    public function get_modification_date()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFICATION_DATE);
    }

    public function get_email_sent()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
    }

    public function set_content_object_id($id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $id);
    }

    public function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    public function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    public function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    public function set_publisher_id($publisher_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    public function set_publication_date($publication_date)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_DATE, $publication_date);
    }

    public function set_modification_date($modification_date)
    {
        $this->set_default_property(self :: PROPERTY_MODIFICATION_DATE, $modification_date);
    }

    public function set_email_sent($email_sent)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL_SENT, $email_sent);
    }

    public function get_content_object()
    {
        if (! isset($this->content_object))
        {
            $this->content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $this->get_content_object_id());
        }

        return $this->content_object;
    }

    public function get_publisher()
    {
        if (! isset($this->publisher))
        {
            $this->publisher = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                User :: class_name(),
                (int) $this->get_publisher_id());
        }

        return $this->publisher;
    }

    /**
     * Sets the publication publisher for caching
     *
     * @param $user; User
     */
    public function set_publisher(User $user)
    {
        $this->publisher = $user;
    }

    public function was_email_sent()
    {
        return $this->get_email_sent();
    }

    public function create()
    {
        $this->set_publication_date(time());

        if (! parent :: create())
        {
            return false;
        }

        $parent = Rights :: getInstance()->get_root_id(self :: package());

        return Rights :: getInstance()->create_location(
            self :: package(),
            Rights :: TYPE_PUBLICATION,
            $this->get_id(),
            false,
            $parent);
    }

    public function delete()
    {
        $location = Rights :: getInstance()->get_location_by_identifier(
            self :: package(),
            Rights :: TYPE_PUBLICATION,
            $this->get_id());
        if ($location)
        {
            if (! $location->delete())
            {
                return false;
            }
        }

        if (! parent :: delete())
        {
            return false;
        }

        return true;
    }

    public function is_hidden()
    {
        return $this->get_hidden();
    }

    public function is_forever()
    {
        return $this->get_from_date() == 0 && $this->get_to_date() == 0;
    }

    /**
     * Toggles the visibility of this publication.
     */
    public function toggle_visibility()
    {
        $this->set_hidden(! $this->is_hidden());
    }

    public function is_visible_for_target_users()
    {
        return (! $this->is_hidden()) &&
             ($this->is_forever() || ($this->get_from_date() <= time() && time() <= $this->get_to_date()));
    }

    public function is_for_everybody()
    {
        return (count($this->get_target_users()) == 0 && count($this->get_target_groups()) == 0);
    }

    /**
     * Gets the list of target users of this publication
     *
     * @return multitype:int An array of user ids.
     * @see is_for_everybody()
     */
    public function get_target_users()
    {
        if (! isset($this->target_users))
        {
            $this->target_users = DataManager :: retrieve_publication_target_user_ids($this->get_id());
        }

        return $this->target_users;
    }

    /**
     * Gets the list of target groups of this publication
     *
     * @return multitype:int An array of group ids.
     * @see is_for_everybody()
     */
    public function get_target_groups()
    {
        if (! isset($this->target_groups))
        {
            $this->target_groups = DataManager :: retrieve_publication_target_platform_group_ids($this->get_id());
        }

        return $this->target_groups;
    }

    /**
     *
     * @param multitype:int $target_users
     */
    public function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    /**
     *
     * @param multitype:int $target_groups
     */
    public function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }
}
