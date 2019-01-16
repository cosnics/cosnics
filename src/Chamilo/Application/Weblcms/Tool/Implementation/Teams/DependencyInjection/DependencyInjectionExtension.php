<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\DependencyInjection;

use Chamilo\Libraries\File\Path;
use Hogent\Application\Weblcms\Tool\Implementation\Survey\Manager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @inheritdoc
 */
class DependencyInjectionExtension extends Extension implements ExtensionInterface
{

    /**
     * @inheritdoc
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container, new FileLocator(
                Path::getInstance()->namespaceToFullPath(Manager::context()) .
                'Resources/Configuration/DependencyInjection'
            )
        );

        $loader->load('services.xml');
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *
     * @api
     */
    public function getAlias()
    {
        return 'chamilo.application.weblcms.tool.implementation.teams';
    }
}