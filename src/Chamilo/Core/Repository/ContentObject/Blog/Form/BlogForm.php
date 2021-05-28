<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Form;

use Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 *
 * @package repository.lib.content_object.blog
 */

/**
 * This class represents a form to create or update blogs
 */
class BlogForm extends ContentObjectForm
{

    // Inherited
    public function create_content_object()
    {
        $object = new Blog();

        $blogLayouts = array_keys(Blog::get_available_blog_layouts());
        $object->set_blog_layout($blogLayouts[0]);

        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $blog = $this->get_content_object();
        if (isset($blog))
        {
            $defaults[Blog::PROPERTY_BLOG_LAYOUT] = $blog->get_blog_layout();
        }
        parent::setDefaults($defaults);
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();

        $blogLayouts = array_keys(Blog::get_available_blog_layouts());
        $object->set_blog_layout($blogLayouts[0]);

        return parent::update_content_object();
    }
}
