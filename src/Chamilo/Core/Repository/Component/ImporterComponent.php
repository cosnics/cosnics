<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportService;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: importer.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class ImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! RightsService::getInstance()->canAddContentObjects($this->get_user(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $type = $this->getRequest()->getFromUrl(self::PARAM_IMPORT_TYPE, 'cpo');
        $contentObjectImportService = new ContentObjectImportService($type, $this->getWorkspace(), $this);

        if ($contentObjectImportService->hasFinished())
        {
            // Session :: register(self :: PARAM_MESSAGES, $controller->get_messages_for_url());
            $this->simple_redirect(array(self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS));
        }
        else
        {
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(),
                    Translation::get(
                        'ImportType',
                        array(
                            'TYPE' => Translation::get(
                                'ImportType' . StringUtilities::getInstance()->createString($type)->upperCamelize())))));

            $html = array();

            $html[] = $this->render_header();
            $html[] = $contentObjectImportService->renderForm();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        return null;
    }

    public function get_additional_parameters($additionalParameters = array())
    {
        return [self::PARAM_IMPORT_TYPE];
    }
}
