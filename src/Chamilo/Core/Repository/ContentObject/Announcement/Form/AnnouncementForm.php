<?php
namespace Chamilo\Core\Repository\ContentObject\Announcement\Form;

use Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * $Id: announcement_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.announcement
 */
/**
 * This class represents a form to create or update announcements
 */
class AnnouncementForm extends ContentObjectForm
{
    
    // Inherited
    public function create_content_object()
    {
        $object = new Announcement();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
