<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Table\Share\ShareTable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

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
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        if (! empty($selectedWorkspaceIdentifiers))
        {
            $selectedContentObjectIdentifiers = (array) $this->getRequest()->get(
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID,
                array());

            $selectedContentObjectNumbers = DataManager::distinct(
                ContentObject::class_name(),
                new DataClassDistinctParameters(
                    new InCondition(
                        new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                        $selectedContentObjectIdentifiers),
                    new DataClassProperties(
                        array(
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER)))));

            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());

            foreach ($selectedWorkspaceIdentifiers as $selectedWorkspaceIdentifier)
            {
                foreach ($selectedContentObjectNumbers as $selectedContentObjectNumber)
                {
                    $contentObjectRelationService->createContentObjectRelation(
                        $selectedWorkspaceIdentifier,
                        $selectedContentObjectNumber,
                        0);
                }
            }

            $this->redirect(
                Translation::get('ContentObjectsShared'),
                false,
                array(
                    self::PARAM_ACTION => null,
                    \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_BROWSE_CONTENT_OBJECTS));
        }
        else
        {
            $contentObjectIdentifiers = $this->getSelectedContentObjectIdentifiers();

            if (count($contentObjectIdentifiers) >= 1)
            {
                $contentObjects = DataManager::retrieves(
                    ContentObject::class_name(),
                    new DataClassRetrievesParameters(
                        new InCondition(
                            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                            $contentObjectIdentifiers)));

                $toolbar = new Toolbar(Toolbar::TYPE_VERTICAL);

                while ($contentObject = $contentObjects->next_result())
                {
                    $viewUrl = new Redirect(
                        array(
                            Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::context(),
                            \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS,
                            \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId()));

                    $toolbar->add_item(
                        new ToolbarItem(
                            $contentObject->get_title(),
                            Theme::getInstance()->getImagePath($contentObject->package(), 'Logo/16'),
                            $viewUrl->getUrl(),
                            ToolbarItem::DISPLAY_ICON_AND_LABEL,
                            false,
                            null,
                            '_blank'));
                }

                $selectedObjectsPreviews = array();

                $selectedObjectsPreviews[] = '<div class="panel panel-default">';
                $selectedObjectsPreviews[] = '<div class="panel-heading">';
                $selectedObjectsPreviews[] = '<h3 class="panel-title">';
                $selectedObjectsPreviews[] = Translation::get('SelectedContentObjects');
                $selectedObjectsPreviews[] = '</h3>';
                $selectedObjectsPreviews[] = '</div>';
                $selectedObjectsPreviews[] = '<div class="panel-body">';
                $selectedObjectsPreviews[] = $toolbar->as_html();
                $selectedObjectsPreviews[] = '</div>';
                $selectedObjectsPreviews[] = '</div>';

                $selectedObjectsPreview = implode(PHP_EOL, $selectedObjectsPreviews);
            }

            $table = new ShareTable($this);

            $html = array();

            $html[] = $this->render_header();

            $parameters = array();
            $parameters[self::PARAM_CONTEXT] = Manager::context();
            $parameters[self::PARAM_ACTION] = self::ACTION_CREATE;

            $redirect = new Redirect($parameters);
            $url = $redirect->getUrl();

            $html[] = '<div class="alert alert-info" role="alert">' .
                 $this->getTranslation('ShareInformation', array('WORKSPACE_URL' => $url)) . '</div>';

            $html[] = $selectedObjectsPreview;
            $html[] = '<h3 style="margin-bottom: 30px;">' . $this->getTranslation('ShareInWorkspaces') . '</h3>';
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
        return array(\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @return integer[]
     */
    public function getSelectedWorkspaceIdentifiers()
    {
        if (! isset($this->selectedWorkspaceIdentifiers))
        {
            $this->selectedWorkspaceIdentifiers = (array) $this->getRequest()->get(
                Manager::PARAM_SELECTED_WORKSPACE_ID,
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
            $this->selectedContentObjectIdentifiers = (array) $this->getRequest()->get(
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID,
                array());
        }

        return $this->selectedContentObjectIdentifiers;
    }

    /**
     * Translation method helper
     *
     * @param string $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array())
    {
        return Translation::getInstance()->getTranslation($variable, $parameters, Manager::context());
    }
}
