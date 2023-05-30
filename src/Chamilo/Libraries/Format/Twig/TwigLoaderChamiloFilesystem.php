<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Exception;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * This class loads twig templates by using a custom made format.
 * The templates are loaded just-in-time by prefixing
 * the template with a package namespace.
 *
 * @package Chamilo\Libraries\Format\Twig
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TwigLoaderChamiloFilesystem implements LoaderInterface
{

    public function exists(string $name)
    {
        try
        {
            $this->findTemplate($name);

            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * Finds a template based on a given template name.
     * The template name is prefixed by a namespace:
     *
     * @throws LoaderError
     */
    protected function findTemplate(string $templateName): string
    {
        if (strpos($templateName, ':') === false)
        {
            throw new LoaderError(
                sprintf(
                    'The given template name "%s" does not include a valid namespace. ' .
                    'The valid format is "namespace:template"', $templateName
                )
            );
        }

        $templateNameParts = explode(':', $templateName);

        $namespace = $templateNameParts[0];

        if (empty($namespace))
        {
            throw new LoaderError(
                sprintf('The namespace in the template name "%s" can not be empty', $templateName)
            );
        }

        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $namespacePath = $systemPathBuilder->namespaceToFullPath($namespace);

        if (!file_exists($namespacePath) || !is_dir($namespacePath))
        {
            throw new LoaderError(
                sprintf(
                    'The given namespace "%s" does not exist or can not be found at the expected location "%s"',
                    $namespace, $namespacePath
                )
            );
        }

        $templatePath = $templateNameParts[1];

        if (empty($templatePath))
        {
            throw new LoaderError(
                sprintf('The template path in the template name "%s" can not be empty', $templateName)
            );
        }

        $fullPath = $namespacePath . 'Resources/Templates/' . $templatePath;

        if (!file_exists($fullPath))
        {
            throw new LoaderError(
                sprintf('The given template "%s" can not be found at the expected path "%s"', $templateName, $fullPath)
            );
        }

        return $fullPath;
    }

    public function getCacheKey(string $name): string
    {
        return $this->findTemplate($name);
    }

    /**
     * @throws LoaderError
     */
    public function getSourceContext(string $name): Source
    {
        $path = $this->findTemplate($name);

        return new Source(file_get_contents($path), $name, $path);
    }

    /**
     * @throws \Twig\Error\LoaderError
     */
    public function isFresh(string $name, int $time): bool
    {
        return filemtime($this->findTemplate($name)) <= $time;
    }
}