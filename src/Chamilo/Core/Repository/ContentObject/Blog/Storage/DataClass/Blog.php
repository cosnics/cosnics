<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\BlogItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

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
    const CLASS_NAME = __CLASS__;
    const PROPERTY_BLOG_LAYOUT = 'blog_layout';

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
    }

    public function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = BlogItem :: class_name();
        return $allowed_types;
    }

    public function get_blog_layout()
    {
        return $this->get_additional_property(self :: PROPERTY_BLOG_LAYOUT);
    }

    public function set_blog_layout($blog_layout)
    {
        return $this->set_additional_property(self :: PROPERTY_BLOG_LAYOUT, $blog_layout);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_BLOG_LAYOUT);
    }

    public static function get_available_blog_layouts()
    {
        $blog_layouts = array();
        
        $dir = __DIR__ . '/../../../../display/php/lib/manager/component/viewer/blog_layout/';
        $files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES);
        foreach ($files as $file)
        {
            $file = basename($file);
            if (substr($file, 0, 1) == '.')
            {
                continue;
            }
            
            $type = substr($file, 0, - 22);
            $blog_layouts[$type] = Translation :: get(
                (string) StringUtilities :: getInstance()->createString($type)->upperCamelize() . 'BlogLayout');
        }
        
        return $blog_layouts;
    }
}
