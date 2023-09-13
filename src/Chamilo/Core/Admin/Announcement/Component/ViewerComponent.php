<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Utilities\StringUtilities;

class ViewerComponent extends Manager implements NoContextComponent
{

    protected ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $id = $this->getRequest()->query->get(self::PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $this->set_parameter(self::PARAM_SYSTEM_ANNOUNCEMENT_ID, $id);

        if (!$this->getRightsService()->canUserIdentifierViewPublication($this->getUser()->getId(), $id))
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        if ($id)
        {
            $publication = $this->getPublicationService()->findPublicationByIdentifier($id);
            $object = $publication->get_content_object();

            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $this->getButtonToolbarRenderer($publication)->render();
            $html[] = ContentObjectRenditionImplementation::launch(
                $object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL
            );
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    $translator->trans(
                        'NoObjectSelected',
                        ['OBJECT' => $translator->trans('SystemAnnouncement', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function getButtonToolbarRenderer($publication): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();

            $translator = $this->getTranslator();

            if ($this->getUser()->isPlatformAdmin() || $publication->get_publisher_id() == $this->getUser()->getId())
            {
                $commonActions->addButton(
                    new Button(
                        $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                        $this->get_url(
                            [
                                self::PARAM_ACTION => self::ACTION_EDIT,
                                self::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id()
                            ]
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $commonActions->addButton(
                    new Button(
                        $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->get_url(
                            [
                                self::PARAM_ACTION => self::ACTION_DELETE,
                                self::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id()
                            ]
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                if ($publication->is_hidden())
                {
                    $glyph = new FontAwesomeGlyph('eye', ['text-muted']);
                }
                elseif ($publication->is_forever())
                {
                    $glyph = new FontAwesomeGlyph('eye');
                }
                else
                {
                    $glyph = new FontAwesomeGlyph('clock');
                }

                $commonActions->addButton(
                    new Button(
                        $translator->trans('Hide', [], StringUtilities::LIBRARIES), $glyph, $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_HIDE,
                            self::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id()
                        ]
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }
}
