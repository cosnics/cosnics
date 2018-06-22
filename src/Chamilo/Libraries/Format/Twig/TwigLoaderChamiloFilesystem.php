<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Libraries\File\Path;
use Twig_Error_Loader;
use Twig_Source;

/**
 * This class loads twig templates by using a custom made format.
 * The templates are loaded just-in-time by prefixing
 * the template with a package namespace.
 *
 * @package Chamilo\Libraries\Format\Twig
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TwigLoaderChamiloFilesystem implements \Twig_LoaderInterface
{

    /**
     *
     * @see Twig_LoaderInterface::getCacheKey()
     */
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    /**
     *
     * @see Twig_LoaderInterface::isFresh()
     */
    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) <= $time;
    }

    /**
     * Finds a template based on a given template name.
     * The template name is prefixed by a namespace:
     *
     * @param string $templateName
     * @throws \Twig_Error_Loader
     * @return string
     */
    protected function findTemplate($templateName)
    {
        if (strpos($templateName, ':') === false)
        {
            throw new \Twig_Error_Loader(
                sprintf(
                    'The given template name "%s" does not include a valid namespace. ' .
                         'The valid format is "namespace:template"',
                        $templateName));
        }

        $templateNameParts = explode(':', $templateName);

        $namespace = $templateNameParts[0];

        if (empty($namespace))
        {
            throw new \Twig_Error_Loader(
                sprintf('The namespace in the template name "%s" can not be empty', $templateName));
        }

        $namespacePath = Path::getInstance()->namespaceToFullPath($namespace);

        if (! file_exists($namespacePath) || ! is_dir($namespacePath))
        {
            throw new \Twig_Error_Loader(
                sprintf(
                    'The given namespace "%s" does not exist or can not be found at the expected location "%s"',
                    $namespace,
                    $namespacePath));
        }

        $templatePath = $templateNameParts[1];

        if (empty($templatePath))
        {
            throw new \Twig_Error_Loader(
                sprintf('The template path in the template name "%s" can not be empty', $templateName));
        }

        $fullPath = $namespacePath . 'Resources/Templates/' . $templatePath;

        if (! file_exists($fullPath))
        {
            throw new \Twig_Error_Loader(
                sprintf('The given template "%s" can not be found at the expected path "%s"', $templateName, $fullPath));
        }

        return $fullPath;
    }

    /**
     * Returns the source context for a given template logical name.
     *
     * @param string $name The template logical name
     *
     * @return Twig_Source
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSourceContext($name)
    {
        $path = $this->findTemplate($name);
        return new Twig_Source(file_get_contents($path), $name, $path);
    }

    /**
     * Check if we have the source code of a template, given its name.
     *
     * @param string $name The name of the template to check if we can load
     *
     * @return bool If the template source code is handled by this loader or not
     */
    public function exists($name)
    {
        try
        {
            $this->findTemplate($name);
            return true;
        }
        catch(\Exception $ex)
        {
            return false;
        }
    }
}