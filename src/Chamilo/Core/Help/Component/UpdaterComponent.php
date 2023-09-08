<?php
namespace Chamilo\Core\Help\Component;

use Chamilo\Core\Help\Form\HelpItemForm;
use Chamilo\Core\Help\Manager;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * @package Chamilo\Core\Help\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdaterComponent extends Manager
{

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $helpService = $this->getHelpService();
        $translator = $this->getTranslator();

        $helpItemIdentifier = $this->getRequest()->query->get(Manager::PARAM_HELP_ITEM);
        $helpItem = $helpService->retrieveHelpItemByIdentifier($helpItemIdentifier);

        if ($helpItemIdentifier && $helpItem instanceof HelpItem)
        {
            $form = new HelpItemForm($helpItem, $this->get_url([Manager::PARAM_HELP_ITEM => $helpItemIdentifier]));

            if ($form->validate())
            {
                $result = $helpService->updateHelpItemFromValues($helpItem, $form->exportValues());

                $this->redirectWithMessage(
                    $translator->trans($result ? 'HelpItemUpdated' : 'HelpItemNotUpdated', [], Manager::CONTEXT),
                    !$result, [Application::PARAM_ACTION => Manager::ACTION_BROWSE_HELP_ITEMS]
                );
            }
            else
            {
                $html = [];

                $html[] = $this->renderHeader();
                $html[] =
                    '<h4>' . $translator->trans('UpdateItem', [], Manager::CONTEXT) . ': ' . $helpItem->get_context() .
                    ' - ' . $helpItem->get_identifier() . '</h4>';
                $html[] = $form->render();
                $html[] = $this->renderFooter();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(
                htmlentities($translator->trans('NoHelpItemSelected', [], Manager::CONTEXT))
            );
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => Manager::ACTION_BROWSE_HELP_ITEMS]),
                $this->getTranslator()->trans('HelpManagerBrowserComponent', [], Manager::CONTEXT)
            )
        );
    }
}
