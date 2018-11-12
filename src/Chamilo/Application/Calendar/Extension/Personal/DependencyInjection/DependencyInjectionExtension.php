<?php
namespace Chamilo\Application\Calendar\Extension\Personal\DependencyInjection;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends Extension implements ExtensionInterface
{

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'chamilo.application.calendar.extension.personal';
    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));

        $loader = new XmlFileLoader(
            $container, new FileLocator(
                $pathBuilder->getConfigurationPath('Chamilo\Application\Calendar\Extension\Personal') .
                'DependencyInjection'
            )
        );

        $loader->load('services.xml');
    }
}