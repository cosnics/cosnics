<?php
namespace Chamilo\Libraries\Filesystem\Factory;

use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * @package Chamilo\Libraries\Filesystem\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlPurifierFactory
{
    public function __construct(protected ConfigurablePathBuilder $configurablePathBuilder)
    {
    }

    public function buildHtmlPurifier(): HTMLPurifier
    {
        $configuration = HTMLPurifier_Config::createDefault();
        $configuration->set(
            'Cache.SerializerPath', $this->configurablePathBuilder->getCachePath(StringUtilities::LIBRARIES . '\Rss')
        );
        $configuration->set('Cache.SerializerPermissions', 06770);

        return new HTMLPurifier($configuration);
    }
}