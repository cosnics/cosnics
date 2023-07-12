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
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ViewerComponent extends Manager implements NoContextComponent
{

    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = $this->getRequest()->query->get(self::PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $this->set_parameter(self::PARAM_SYSTEM_ANNOUNCEMENT_ID, $id);

        if (!$this->getRightsService()->canUserIdentifierViewPublication($this->getUser()->getId(), $id))
        {
            throw new NotAllowedException();
        }

        if ($id)
        {
            $publication = $this->getPublicationService()->findPublicationByIdentifier((int) $id);
            $object = $publication->get_content_object();

            $html = [];

            $html[] = $this->render_header();
            $html[] = $this->getButtonToolbarRenderer($publication)->render();
            $html[] = ContentObjectRenditionImplementation::launch(
                $object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL, $this
            );
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', array('OBJECT' => Translation::get('SystemAnnouncement')),
                        StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function getButtonToolbarRenderer($publication)
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();

            if ($this->get_user()->isPlatformAdmin() ||
                $publication->get_publisher_id() == $this->get_user()->get_id())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_EDIT,
                                self::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id()
                            )
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $commonActions->addButton(
                    new Button(
                        Translation::get('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_DELETE,
                                self::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id()
                            )
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                if ($publication->is_hidden())
                {
                    $glyph = new FontAwesomeGlyph('eye', array('text-muted'));
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
                        Translation::get('Hide', [], StringUtilities::LIBRARIES), $glyph, $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_HIDE,
                            self::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id()
                        )
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
