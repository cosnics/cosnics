<?php
namespace Chamilo\Core\Repository\Viewer\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ViewerComponent extends Manager
{

    public function run()
    {
        $contentObjectIdentifier = $this->getRequest()->query->get(self::PARAM_VIEW_ID);

        if ($contentObjectIdentifier)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $contentObjectIdentifier
            );

            $canEditContentObject = RightsService::getInstance()->canEditContentObject(
                $this->get_user(),
                $content_object
            );
            $canUseContentObject =
                RightsService::getInstance()->canUseContentObject($this->get_user(), $content_object);

            $buttonToolBar = new ButtonToolBar();

            if ($canUseContentObject)
            {
                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('Publish', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Publish'),
                        $this->get_url(
                            array_merge($this->get_parameters(), array(self::PARAM_ID => $content_object->get_id())),
                            false
                        ),
                        Button::DISPLAY_ICON_AND_LABEL,
                        false,
                        'btn-primary'
                    )
                );
            }

            if ($canEditContentObject && $canUseContentObject)
            {
                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('EditAndPublish'),
                        Theme::getInstance()->getCommonImagePath('Action/Editpublish'),
                        $this->get_url(
                            array_merge(
                                $this->get_parameters(),
                                array(
                                    self::PARAM_TAB => self::TAB_CREATOR,
                                    self::PARAM_ACTION => self::ACTION_CREATOR,
                                    self::PARAM_EDIT_ID => $content_object->get_id()
                                )
                            )
                        )
                    )
                );
            }

            $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

            $html = array();

            $html[] = $this->render_header();
            $html[] = ContentObjectRenditionImplementation::launch(
                $content_object,
                ContentObjectRendition::FORMAT_HTML,
                ContentObjectRendition::VIEW_FULL,
                $this
            );
            $html[] = $buttonToolbarRenderer->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * @param ContentObject $attachment
     *
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        if(!$attachment instanceof ContentObject)
        {
            return null;
        }

        $contentObjectIdentifier = $this->getRequest()->query->get(self::PARAM_VIEW_ID);

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => 'Chamilo\Core\Repository',
                Application::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_VIEW_ATTACHMENT,
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $contentObjectIdentifier,
                \Chamilo\Core\Repository\Manager::PARAM_ATTACHMENT_ID => $attachment->getId()
            )
        );

        return $redirect->getUrl();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repo_viewer_viewer');
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSER)),
                Translation::get('BrowserComponent')
            )
        );
    }
}
