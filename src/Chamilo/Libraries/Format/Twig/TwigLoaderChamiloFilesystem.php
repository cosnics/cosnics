<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Libraries\File\Path;

/**
 * This class loads twig templates by using a custom made format.
 * The templates are loaded just-in-time by prefixing
 * the template with a package namespace.
 * 
 * @package Chamilo\Libraries\Format\Twig$TwigLoaderChamiloFilesystem
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TwigLoaderChamiloFilesystem implements \Twig_LoaderInterface
{

    /**
     * Gets the source code of a template, given its name.
     * 
     * @param string $name The name of the template to load
     * @return string The template source code
     * @throws \Twig_Error_Loader When $name is not found
     */
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     * 
     * @param string $name The name of the template to load
     * @return string The cache key
     * @throws \Twig_Error_Loader When $name is not found
     */
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    /**
     * Returns true if the template is still fresh.
     * 
     * @param string $name The template name
     * @param int $time The last modification time of the cached template
     * @return Boolean true if the template is fresh, false otherwise
     * @throws \Twig_Error_Loader When $name is not found
     */
    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) <= $time;
    }

    /**
     * Finds a template based on a given template name.
     * The template name is prefixed by a namespace:
     * 
     * @example common\libraries:header.html.twig
     * @param string $template_name
     *
     * @throws \Twig_Error_Loader
     *
     * @return string
     */
    protected function findTemplate($template_name)
    {
        if (strpos($template_name, ':') === false)
        {
            throw new \Twig_Error_Loader(
                sprintf(
                    'The given template name "%s" does not include a valid namespace. ' .
                         'The valid format is "namespace:template"', 
                        $template_name));
        }
        
        $template_name_parts = explode(':', $template_name);
        
        $namespace = $template_name_parts[0];
        
        if (empty($namespace))
        {
            throw new \Twig_Error_Loader(
                sprintf('The namespace in the template name "%s" can not be empty', $template_name));
        }
        
        $namespace_path = Path::getInstance()->namespaceToFullPath($namespace);
        
        if (! file_exists($namespace_path) || ! is_dir($namespace_path))
        {
            throw new \Twig_Error_Loader(
                sprintf(
                    'The given namespace "%s" does not exist or can not be found at the expected location "%s"', 
                    $namespace, 
                    $namespace_path));
        }
        
        $template_path = $template_name_parts[1];
        
        if (empty($template_path))
        {
            throw new \Twig_Error_Loader(
                sprintf('The template path in the template name "%s" can not be empty', $template_name));
        }
        
        $full_path = $namespace_path . 'Resources/Templates/' . $template_path;
        
        if (! file_exists($full_path))
        {
            throw new \Twig_Error_Loader(
                sprintf(
                    'The given template "%s" can not be found at the expected path "%s"', 
                    $template_name, 
                    $full_path));
        }
        
        return $full_path;
    }
}