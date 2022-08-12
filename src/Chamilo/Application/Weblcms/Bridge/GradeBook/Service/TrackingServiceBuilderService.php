<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class TrackingServiceBuilderService
{
    /**
     * @param ContentObjectPublication $publication
     * @return TrackingService
     *
     * @throws \Exception
     */
    public function buildTrackingServiceForPublication(ContentObjectPublication $publication): TrackingService
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        /** @var DataClassRepository */
        $dataClassRepository = $container->get('chamilo.libraries.storage.data_manager.doctrine.data_class_repository');
        $trackingServiceBuilder = new TrackingServiceBuilder($dataClassRepository);
        return $trackingServiceBuilder->buildTrackingService(new TrackingParameters((int)$publication->getId()));
    }
}