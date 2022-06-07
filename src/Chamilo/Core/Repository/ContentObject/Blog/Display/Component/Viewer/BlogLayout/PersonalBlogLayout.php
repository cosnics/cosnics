<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Component\Viewer\BlogLayout;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Blog\Display\Component\Viewer\BlogLayout;
use Chamilo\Core\Repository\ContentObject\Blog\Display\Manager;
use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * A personal blog layout with the user picture on the side
 */
class PersonalBlogLayout extends BlogLayout
{
    /**
     * @param \Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem $complexBlogItem
     *
     * @return string
     */
    public function determinePanelClasses(ComplexBlogItem $complexBlogItem)
    {
        $classes = [];

        $classes[] = 'panel';
        $classes[] = 'panel-default';
        $classes[] = 'panel-publication';

        return implode(' ', $classes);
    }

    /**
     * @param integer $date
     *
     * @return string
     */
    public function formatDate($date)
    {
        $date_format = Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES);

        return DatetimeUtilities::getInstance()->formatLocaleDate($date_format, $date);
    }

    /**
     * @return \Chamilo\Core\User\Picture\UserPictureProviderInterface
     */
    public function getUserPictureProvider()
    {
        return $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $attachment
     *
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->get_parent()->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_VIEW_ATTACHMENT,
                Manager::PARAM_ATTACHMENT_ID => $attachment->getId(),
                Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_parent()->getRequest()->query->get(
                    Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                )
            )
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem $complexBlogItem
     *
     * @return string
     */
    public function renderBlogItem(ComplexBlogItem $complexBlogItem)
    {
        $html = [];

        $html[] = '<div class="' . $this->determinePanelClasses($complexBlogItem) . '">';
        $html[] = '<div class="panel-body">';

        $html[] = $this->renderBlogItemHeader($complexBlogItem);
        $html[] = $this->renderBlogItemBody($complexBlogItem);
        $html[] = $this->renderBlogItemFooter($complexBlogItem);

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderBlogItemAuthorName(ComplexBlogItem $complexBlogItem)
    {
        $blogItem = $complexBlogItem->get_ref_object();
        $author = DataManager::retrieve_by_id(
            User::class, (int) $blogItem->get_owner_id()
        );

        if ($author instanceof User)
        {
            return $author->get_fullname();
        }
        else
        {
            return Translation::get('AuthorUnknown');
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem $complexBlogItem
     *
     * @return string
     */
    public function renderBlogItemBody(ComplexBlogItem $complexBlogItem)
    {
        $blogItem = $complexBlogItem->get_ref_object();

        $html = [];

        $html[] = '<div class="row panel-publication-body">';
        $html[] = '<div class="col-xs-12">';

        $this->get_parent()->getRequest()->query->set(
            Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complexBlogItem->getId()
        );

        $renditionImplementation = ContentObjectRenditionImplementation::factory(
            $blogItem, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
        );

        $html[] = $renditionImplementation->render();

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem $complexBlogItem
     *
     * @return string
     */
    public function renderBlogItemFooter(ComplexBlogItem $complexBlogItem)
    {
        $html = [];

        $html[] = '<div class="row panel-publication-footer">';

        $html[] = '<div class="col-xs-12 panel-publication-footer-date">';
        $html[] = $this->renderVisiblePublicationDate($complexBlogItem);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem $complexBlogItem
     *
     * @return string
     */
    public function renderBlogItemHeader(ComplexBlogItem $complexBlogItem)
    {
        $blogItem = $complexBlogItem->get_ref_object();

        $html = [];

        $html[] = '<div class="row panel-publication-header">';

        $html[] = '<div class="col-xs-12 col-sm-10 panel-publication-header-title">';

        $author = DataManager::retrieve_by_id(User::class, (int) $blogItem->get_owner_id());
        $userPictureProvider = $this->getUserPictureProvider();

        $html[] =
            '<img class="img-rounded pull-left" src="' . $userPictureProvider->getUserPictureAsBase64String($author, $author) .
            '" >';

        $html[] = '<h3>' . $blogItem->get_title() . '</h3>';
        $html[] = '<small>' . $this->renderBlogItemAuthorName($complexBlogItem) . '</small>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-sm-2 panel-publication-header-actions">';
        $html[] = $this->renderBlogItemActions($complexBlogItem);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem $complexBlogItem
     *
     * @return string
     */
    public function renderVisiblePublicationDate(ComplexBlogItem $complexBlogItem)
    {
        $blogItem = $complexBlogItem->get_ref_object();

        $contentObjectModified = $blogItem->get_modification_date() > $blogItem->get_creation_date();

        $html = [];

        $glyphClasses = $contentObjectModified ? array('text-danger') : [];
        $glyph = new FontAwesomeGlyph('clock', $glyphClasses, null, 'far');

        $html[] = $glyph->render();
        $html[] = $this->formatDate($blogItem->get_modification_date());

        return implode(PHP_EOL, $html);
    }
}
