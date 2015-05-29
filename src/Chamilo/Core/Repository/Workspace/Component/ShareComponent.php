<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Table\Share\ShareTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ShareComponent extends Manager implements TableSupport
{

    /**
     *
     * @var integer[]
     */
    private $selectedContentObjectIdentifiers;

    /**
     *
     * @var integer[]
     */
    private $selectedWorkspaceIdentifiers;

    public function run()
    {
        $selectedContentObjectIdentifiers = $this->getSelectedContentObjectIdentifiers();
        $selectedWorkspaceIdentifiers = $this->getSelectedWorkspaceIdentifiers();

        if (empty($selectedContentObjectIdentifiers))
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObject'));
        }

        if (! empty($selectedWorkspaceIdentifiers))
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());

            foreach ($selectedWorkspaceIdentifiers as $selectedWorkspaceIdentifier)
            {
                foreach ($selectedContentObjectIdentifiers as $selectedContentObjectIdentifier)
                {
                    $contentObjectRelationService->createContentObjectRelation(
                        $selectedWorkspaceIdentifier,
                        $selectedContentObjectIdentifier,
                        0);
                }
            }

            $this->redirect(
                Translation :: get('ContentObjectsShared'),
                false,
                array(
                    self :: PARAM_ACTION => null,
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_BROWSE_CONTENT_OBJECTS));
        }
        else
        {
            $contentObjectIdentifiers = $this->getSelectedContentObjectIdentifiers();

            if (count($contentObjectIdentifiers) > 1)
            {
                $contentObjects = DataManager :: retrieves(
                    ContentObject :: class_name(),
                    new DataClassRetrievesParameters(
                        new InCondition(
                            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                            $contentObjectIdentifiers)));

                $toolbar = new Toolbar();

                while ($contentObject = $contentObjects->next_result())
                {
                    $viewUrl = new Redirect(
                        array(
                            Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(),
                            \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_VIEW_CONTENT_OBJECTS,
                            \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID => $contentObject->getId()));

                    $toolbar->add_item(
                        new ToolbarItem(
                            $contentObject->get_title(),
                            Theme :: getInstance()->getImagePath($contentObject->package(), 'Logo/16'),
                            $viewUrl->getUrl(),
                            ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                            false,
                            null,
                            '_blank'));
                }

                $selectedObjectsPreviews = array();

                $selectedObjectsPreviews[] = '<div class="information-box">';
                $selectedObjectsPreviews[] = '<h3>';
                $selectedObjectsPreviews[] = Translation :: get('SelectedContentObjects');
                $selectedObjectsPreviews[] = '</h3>';
                $selectedObjectsPreviews[] = $toolbar->as_html();
                $selectedObjectsPreviews[] = '</div>';

                $selectedObjectsPreview = implode(PHP_EOL, $selectedObjectsPreviews);
            }
            else
            {
                $contentObject = DataManager :: retrieve_by_id(
                    ContentObject :: class_name(),
                    array_pop($contentObjectIdentifiers));

                $renditionImplementation = ContentObjectRenditionImplementation :: factory(
                    $contentObject,
                    ContentObjectRendition :: FORMAT_HTML,
                    ContentObjectRendition :: VIEW_FULL,
                    $this);
                $selectedObjectsPreview = $renditionImplementation->render();
            }

            $table = new ShareTable($this);

            $html = array();

            $html[] = $this->render_header();
            $html[] = $selectedObjectsPreview;
            $html[] = $table->as_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Manager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @return integer[]
     */
    public function getSelectedWorkspaceIdentifiers()
    {
        if (! isset($this->selectedWorkspaceIdentifiers))
        {
            $this->selectedWorkspaceIdentifiers = (array) $this->getRequest()->query->get(
                Manager :: PARAM_SELECTED_WORKSPACE_ID,
                array());
        }

        return $this->selectedWorkspaceIdentifiers;
    }

    /**
     *
     * @return integer[]
     */
    public function getSelectedContentObjectIdentifiers()
    {
        if (! isset($this->selectedContentObjectIdentifiers))
        {
            $this->selectedContentObjectIdentifiers = (array) $this->getRequest()->query->get(
                \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID,
                array());
        }

        return $this->selectedContentObjectIdentifiers;
    }
}
