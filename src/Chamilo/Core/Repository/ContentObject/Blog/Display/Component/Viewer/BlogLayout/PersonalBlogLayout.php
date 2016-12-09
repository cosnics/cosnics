<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Component\Viewer\BlogLayout;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Blog\Display\Component\Viewer\BlogLayout;
use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A personal blog layout with the user picture on the side
 */
class PersonalBlogLayout extends BlogLayout
{

    public function display_blog_item(ComplexBlogItem $complex_blog_item)
    {
        $blog_item = $complex_blog_item->get_ref_object();
        $owner = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            User :: class_name(), 
            (int) $blog_item->get_owner_id());
        
        if ($owner)
        {
            $name = $owner->get_fullname();
            $profilePhotoUrl = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(), 
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE, 
                    \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $owner->get_id()));
            $picture = $profilePhotoUrl->getUrl();
        }
        else
        {
            $name = Translation :: get('AuthorUnknown');
            $picture = Theme :: getInstance()->getCommonImagePath('Unknown');
        }
        
        $html = array();
        
        $html[] = '<div class="blog_item">';
        $html[] = '<div class="information_box">';
        $html[] = '<img class="user_image" src="' . $picture . '" /><br /><br />';
        $html[] = $name . '<br />';
        $html[] = DatetimeUtilities :: format_locale_date(null, $complex_blog_item->get_add_date());
        $html[] = '</div>';
        $html[] = '<div class="message_box">';
        $html[] = '<div class="title">' . $blog_item->get_title() . '</div>';
        $html[] = '<div class="description">';
        
        $renderer = new ContentObjectResourceRenderer($blog_item, $blog_item->get_description());
        $html[] = $renderer->run();
        
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp</div>';
        $html[] = $this->get_attached_content_objects_as_html($complex_blog_item);
        $html[] = '<div class="actions_box">';
        $html[] = '<div class="actions">' . $this->get_blog_item_actions($complex_blog_item) . '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp</div>';
        $html[] = '</div><br />';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Gets the layout of the attachments list
     * 
     * @param BlogItem $blog_item
     */
    public function get_attached_content_objects_as_html($complex_blog_item)
    {
        $blog_item = $complex_blog_item->get_ref_object();
        $attachments = $blog_item->get_attachments();
        if (count($attachments))
        {
            $html[] = '<div class="attachments">';
            $html[] = '<div class="attachments_title">' . htmlentities(
                Translation :: get('Attachements', null, Utilities :: COMMON_LIBRARIES)) . '</div>';
            Utilities :: order_content_objects_by_title($attachments);
            $html[] = '<ul class="attachments_list">';
            
            foreach ($attachments as $attachment)
            {
                $url = $this->get_parent()->get_content_object_display_attachment_url(
                    $attachment, 
                    $complex_blog_item->get_id());
                $url = 'javascript:openPopup(\'' . $url . '\'); return false;';
                $html[] = '<li><a href="#" onClick="' . $url . '"><img src="' . Theme :: getInstance()->getImagePath(
                    ClassnameUtilities :: getInstance()->getNamespaceParent($attachment->get_type(), 3), 
                    'Logo/' . Theme :: ICON_MINI) . '" alt="' .
                     htmlentities(
                        Translation :: get(
                            'TypeName', 
                            null, 
                            ClassnameUtilities :: getInstance()->getNamespaceParent($attachment->get_type(), 3))) .
                     '"/> ' . $attachment->get_title() . '</a></li>';
            }
            
            $html[] = '</ul>';
            $html[] = '</div>';
            
            return implode(PHP_EOL, $html);
        }
        
        return '';
    }
}
