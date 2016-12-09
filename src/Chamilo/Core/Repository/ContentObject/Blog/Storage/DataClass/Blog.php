<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\BlogItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: blog.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.blog
 */
/**
 * This class represents an blog
 */
class Blog extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_BLOG_LAYOUT = 'blog_layout';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = BlogItem::class_name();
        return $allowed_types;
    }

    public function get_blog_layout()
    {
        return $this->get_additional_property(self::PROPERTY_BLOG_LAYOUT);
    }

    public function set_blog_layout($blog_layout)
    {
        return $this->set_additional_property(self::PROPERTY_BLOG_LAYOUT, $blog_layout);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_BLOG_LAYOUT);
    }

    public static function get_available_blog_layouts()
    {
        $blog_layouts = array();
        
        $dir = Path::getInstance()->namespaceToFullPath(self::package() . '\Display\Component\Viewer\BlogLayout');
        $files = Filesystem::get_directory_content($dir, Filesystem::LIST_FILES, false);
        
        foreach ($files as $file)
        {
            $variable = str_replace('.php', '', $file);
            $type = str_replace('BlogLayout.php', '', $file);
            
            $blog_layouts[$type] = Translation::get($variable);
        }
        
        return $blog_layouts;
    }
}
