<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass;

use _PHPStan_8862d57cc\Symfony\Component\Finder\Iterator\FileTypeFilterIterator;
use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\BlogItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupportInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass
 */
class Blog extends ContentObject implements ComplexContentObjectSupportInterface, DataClassExtensionInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Blog';

    public const PROPERTY_BLOG_LAYOUT = 'blog_layout';

    public static function getAdditionalPropertyNames(): array
    {
        return parent::getAdditionalPropertyNames([self::PROPERTY_BLOG_LAYOUT]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_blog';
    }

    public function get_allowed_types(): array
    {
        $allowed_types = [];
        $allowed_types[] = BlogItem::class;

        return $allowed_types;
    }

    public static function get_available_blog_layouts()
    {
        $blog_layouts = [];

        $dependencyInjectionContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        /**
         * @var \Chamilo\Libraries\File\SystemPathBuilder $systemPathBuilder
         */
        $systemPathBuilder = $dependencyInjectionContainer->get(SystemPathBuilder::class);

        /**
         * @var \Chamilo\Libraries\File\FilesystemTools $filesystemTools
         */
        $filesystemTools = $dependencyInjectionContainer->get(FilesystemTools::class);

        $dir = $systemPathBuilder->namespaceToFullPath(Blog::CONTEXT . '\Display\Component\Viewer\BlogLayout');
        $files = $filesystemTools->getDirectoryContent($dir, FileTypeFilterIterator::ONLY_FILES, false);

        foreach ($files as $file)
        {
            $variable = str_replace('.php', '', $file);
            $type = str_replace('BlogLayout.php', '', $file);

            $blog_layouts[$type] = Translation::get($variable);
        }

        return $blog_layouts;
    }

    public function get_blog_layout()
    {
        return $this->getAdditionalProperty(self::PROPERTY_BLOG_LAYOUT);
    }

    public function set_blog_layout($blog_layout)
    {
        return $this->setAdditionalProperty(self::PROPERTY_BLOG_LAYOUT, $blog_layout);
    }
}
