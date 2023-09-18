<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Service\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Utilities\StringUtilities;

class HiderComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

        $translator = $this->getTranslator();

        $ids = $this->getRequest()->query->get(self::PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $this->set_parameter(self::PARAM_SYSTEM_ANNOUNCEMENT_ID, $ids);

        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = [$ids];
            }

            foreach ($ids as $id)
            {
                $publication = $this->getPublicationService()->findPublicationByIdentifier($id);
                $publication->toggle_visibility();

                if (!$this->getPublicationService()->updatePublication($publication))
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ContentObjectNotToggled';
                    $parameter = ['OBJECT' => 'PublicationVisibility'];
                }
                else
                {
                    $message = 'ContentObjectsNotToggled';
                    $parameter = ['OBJECTS' => 'PublicationsVisibility'];
                }
            }
            elseif (count($ids) == 1)
            {
                $message = 'ContentObjectToggled';
                $parameter = ['OBJECT' => 'PublicationsVisibility'];
            }
            else
            {
                $message = 'ContentObjectsToggled';
                $parameter = ['OBJECTS' => 'PublicationsVisibility'];
            }

            $this->redirectWithMessage(
                $translator->trans($message, $parameter, StringUtilities::LIBRARIES), (bool) $failures,
                [self::PARAM_ACTION => self::ACTION_BROWSE]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['OBJECT' => 'Publication'], StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    /**
     * @return \Chamilo\Core\Admin\Service\BreadcrumbGenerator
     */
    public function getBreadcrumbGenerator(): BreadcrumbGeneratorInterface
    {
        return $this->getService(BreadcrumbGenerator::class);
    }
}
