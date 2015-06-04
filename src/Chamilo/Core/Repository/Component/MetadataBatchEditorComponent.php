<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;

/**
 * This component executes the ContentObjectMetadataElementLinkerComponent Submanager
 *
 * @package repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataBatchEditorComponent extends Manager implements ApplicationSupport
{

    /**
     * Executes this component
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    /**
     * Redirects the user away from the batch editor
     *
     * @param bool $success
     * @param string $message
     */
    public function redirect_from_batch_editor($success, $message)
    {
        $this->redirect(
            $message,
            ! $success,
            array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS),
            array(
                self :: PARAM_CONTENT_OBJECT_ID,
                \Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Manager :: PARAM_ACTION));
    }

    /**
     * Returns the additional parameters that need to be registered
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return parent :: get_additional_parameters(array(self :: PARAM_CONTENT_OBJECT_ID));
    }
}
