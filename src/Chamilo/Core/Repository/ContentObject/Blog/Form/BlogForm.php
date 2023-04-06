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

    public function update_content_object()
    {
        $object = $this->get_content_object();

        $blogLayouts = array_keys(Blog::get_available_blog_layouts());
        $object->set_blog_layout($blogLayouts[0]);

        return parent::update_content_object();
    }

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);
        // $this->addElement('category', Translation:: get('Properties', null, Utilities :: COMMON_LIBRARIES));
        // $this->addElement(
        // 'select',
        // Blog::PROPERTY_BLOG_LAYOUT,
        // Translation:: get('BlogLayout'),
        // Blog:: get_available_blog_layouts()
        // );
        // $this->addElement('category');
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($htmleditor_options, $in_tab);
        // $this->addElement('category', Translation:: get('Properties', null, Utilities :: COMMON_LIBRARIES));
        // $this->addElement(
        // 'select',
        // Blog::PROPERTY_BLOG_LAYOUT,
        // Translation:: get('BlogLayout'),
        // Blog:: get_available_blog_layouts()
        // );
        // $this->addElement('category');
    }

    public function setDefaults($defaults = array(), $filter = null)
    {
        $blog = $this->get_content_object();
        if (isset($blog))
        {
            $defaults[Blog::PROPERTY_BLOG_LAYOUT] = $blog->get_blog_layout();
        }
        parent::setDefaults($defaults);
    }
}
