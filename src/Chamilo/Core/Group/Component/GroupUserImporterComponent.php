<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupUserImportForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Component
 * @author  vanpouckesven
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupUserImporterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $form = new GroupUserImportForm($this->get_url());

        if ($form->validate())
        {
            $success = $form->import_group_users();
            $this->redirectWithMessage(
                $this->getTranslator()->trans($success ? 'GroupUserCSVProcessed' : 'GroupUserCSVNotProcessed', [],
                    Manager::CONTEXT) . '<br />' . $form->get_failed_elements(), !$success,
                [Application::PARAM_ACTION => self::ACTION_IMPORT_GROUP_USERS]
            );
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderCsvFormat();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    public function renderCsvFormat(): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<p>' . $translator->trans('CSVMustLookLike', [], Manager::CONTEXT) . ' (' .
            $translator->trans('MandatoryFields', [], Manager::CONTEXT) . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '<b>action</b>;<b>group_code</b>;<b>username</b>';
        $html[] = 'A;Chamilo;admin';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . $translator->trans('Details', [], Manager::CONTEXT) . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . $translator->trans('Action', [], StringUtilities::LIBRARIES) . '</u></b>';
        $html[] = '<br />A: ' . $translator->trans('Add', [], StringUtilities::LIBRARIES);
        $html[] = '<br />D: ' . $translator->trans('Delete', [null], StringUtilities::LIBRARIES);
        $html[] = '</blockquote>';

        return implode(PHP_EOL, $html);
    }
}
