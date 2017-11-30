<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

/**
 *
 * @package core\repository\content_object\assignment\integration\core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Embedder extends ContentObjectEmbedder
{
    use DependencyInjectionContainerTrait;

    /**
     * @return string
     */
    public function render()
    {
        $this->initializeContainer();

        $configuration = new ApplicationConfiguration(
            $this->get_application()->getRequest(), $this->get_application()->getUser(), $this->get_application()
        );

        $configuration->set(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::CONFIGURATION_DATA_PROVIDER,
            new AssignmentDataProvider($this->get_application()->getTranslator())
        );

        $applicationFactory = $this->getApplicationFactory();

        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::context(),
            $configuration
        )->run();
    }

}
