<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Rights;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Storage\DataManager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ViewerComponent extends Manager implements NoContextComponent
{

    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request :: get(self :: PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $this->set_parameter(self :: PARAM_SYSTEM_ANNOUNCEMENT_ID, $id);
        
        if (! Rights :: getInstance()->is_allowed_in_publciation($id, $this->get_user()->get_id()))
        {
            throw new NotAllowedException();
        }
        
        if ($id)
        {
            $publication = DataManager :: retrieve_by_id(Publication :: class_name(), (int) $id);
            $object = $publication->get_content_object();
            
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $this->getButtonToolbarRenderer($publication)->render();
            $html[] = ContentObjectRenditionImplementation :: launch(
                $object, 
                ContentObjectRendition :: FORMAT_HTML, 
                ContentObjectRendition :: VIEW_FULL, 
                $this);
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected', 
                        array('OBJECT' => Translation :: get('SystemAnnouncement')), 
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function getButtonToolbarRenderer($publication)
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            
            if ($this->get_user()->is_platform_admin() || $publication->get_publisher_id() == $this->get_user()->get_id())
            {
                $commonActions->addButton(
                    new Button(
                        Translation :: get('Edit', array(), Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getCommonImagePath('Action/Edit'), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_EDIT, 
                                self :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id())), 
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                
                $commonActions->addButton(
                    new Button(
                        Translation :: get('Delete', array(), Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getCommonImagePath('Action/Delete'), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_DELETE, 
                                self :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id())), 
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                
                if ($publication->is_hidden())
                {
                    $visibility_img = 'Action/Invisible';
                }
                elseif ($publication->is_forever())
                {
                    $visibility_img = 'Action/Visible';
                }
                else
                {
                    $visibility_img = 'Action/Period';
                }
                
                $commonActions->addButton(
                    new Button(
                        Translation :: get('Hide', array(), Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getCommonImagePath($visibility_img), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_HIDE, 
                                self :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->get_id())), 
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        return $this->buttonToolbarRenderer;
    }
}
