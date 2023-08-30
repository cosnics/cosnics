<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.repository.contentobject.assessmentmatchtextquestion.integration.chamilo.core.repository.contentobject.assessment';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment' => ['package.xml']];
    }
}