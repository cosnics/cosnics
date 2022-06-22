<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.application.weblcms.tool.implementation.learning_path';
    }

    public function getConfigurationFiles(): array
    {
        return [];
    }
}