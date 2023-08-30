<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.repository.content_object.learning_path.integration.chamilo.core.repository';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository' => [
                'package.xml',
                'services.xml'
            ]
        ];
    }
}