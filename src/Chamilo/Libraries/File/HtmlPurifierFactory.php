<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Utilities\StringUtilities;
use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * @package Chamilo\Libraries\File
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlPurifierFactory
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function buildHtmlPurifier(): HTMLPurifier
    {
        $configuration = HTMLPurifier_Config::createDefault();
        $configuration->set(
            'Cache.SerializerPath',
            $this->getConfigurablePathBuilder()->getCachePath(StringUtilities::LIBRARIES . '\Rss')
        );
        $configuration->set('Cache.SerializerPermissions', 06770);

        return new HTMLPurifier($configuration);
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }
}