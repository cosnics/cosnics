<?php
namespace Chamilo\Application\Survey\Favourite\Storage\DataClass;

use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Application\Survey\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublicationUserFavourite extends DataClass
{
    
    // Properties
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_USER_ID = 'user_id';

    /**
     *
     * @var \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    private $publication;

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_PUBLICATION_ID, self::PROPERTY_USER_ID));
    }

    /**
     *
     * @return integer
     */
    public function get_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function get_publication()
    {
        if (! isset($this->publication))
        {
            $this->publication = DataManager::retrieve_by_id(Publication::class_name(), $this->get_publication_id());
        }
        
        return $this->publication;
    }

    /**
     *
     * @param integer $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     *
     * @return integer
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param integer $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }
}