<?php
namespace Chamilo\Core\Repository\ContentObject\BlogItem\Form;

use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\BlogItem;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 *
 * @package repository.lib.content_object.blog_item
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * This class represents a form to create or update blog_items
 */
class BlogItemForm extends ContentObjectForm
{

    // Inherited
    public function create_content_object()
    {
        $object = new BlogItem();
        $this->set_content_object($object);
        return parent::create_content_object();
    }
}
