<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Table\ShareTableRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ShareComponent extends Manager
{

    /**
     * @var int[]
     */
    private array $selectedContentObjectIdentifiers;

    /**
     * @var int[]
     */
    private array $selectedWorkspaceIdentifiers;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \ReflectionException
     * @throws \QuickformException
     * @throws \Exception
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $selectedContentObjectIdentifiers = $this->getSelectedContentObjectIdentifiers();
        $selectedWorkspaceIdentifiers = $this->getSelectedWorkspaceIdentifiers();

        if (empty($selectedContentObjectIdentifiers))
        {
            throw new NoObjectSelectedException($translator->trans('ContentObject', [], Manager::CONTEXT));
        }

        if (!empty($selectedWorkspaceIdentifiers))
        {
            $selectedContentObjectIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID, []
            );

            $selectedContentObjectNumbers = DataManager::distinct(
                ContentObject::class, new DataClassDistinctParameters(
                    new InCondition(
                        new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID),
                        $selectedContentObjectIdentifiers
                    ), new RetrieveProperties(
                        [
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER)
                        ]
                    )
                )
            );

            foreach ($selectedWorkspaceIdentifiers as $selectedWorkspaceIdentifier)
            {
                foreach ($selectedContentObjectNumbers as $selectedContentObjectNumber)
                {
                    $this->getContentObjectRelationService()->createContentObjectRelationFromParameters(
                        (string) $selectedWorkspaceIdentifier, $selectedContentObjectNumber, '0'
                    );
                }
            }

            $this->redirectWithMessage(
                $translator->trans('ContentObjectsShared', [], Manager::CONTEXT), false, [
                    self::PARAM_ACTION => null,
                    Application::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_BROWSE_CONTENT_OBJECTS
                ]
            );
        }
        else
        {
            $contentObjectIdentifiers = $this->getSelectedContentObjectIdentifiers();

            if (count($contentObjectIdentifiers) >= 1)
            {
                $contentObjects = DataManager::retrieves(
                    ContentObject::class, new DataClassRetrievesParameters(
                        new InCondition(
                            new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID),
                            $contentObjectIdentifiers
                        )
                    )
                );

                $toolbar = new Toolbar(Toolbar::TYPE_VERTICAL);

                foreach ($contentObjects as $contentObject)
                {

                    $viewUrl = $this->getUrlGenerator()->fromParameters(
                        [
                            Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::CONTEXT,
                            Application::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS,
                            \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId()
                        ]
                    );

                    $toolbar->add_item(
                        new ToolbarItem(
                            $contentObject->get_title(), $contentObject->getGlyph(
                            IdentGlyph::SIZE_MINI, true, ['fa-fw']
                        ), $viewUrl, ToolbarItem::DISPLAY_ICON_AND_LABEL, false, null, '_blank'
                        )
                    );
                }

                $selectedObjectsPreviews = [];

                $selectedObjectsPreviews[] = '<div class="panel panel-default">';
                $selectedObjectsPreviews[] = '<div class="panel-heading">';
                $selectedObjectsPreviews[] = '<h3 class="panel-title">';
                $selectedObjectsPreviews[] =
                    $translator->trans('SelectedContentObjects', [], \Chamilo\Core\Repository\Manager::CONTEXT);
                $selectedObjectsPreviews[] = '</h3>';
                $selectedObjectsPreviews[] = '</div>';
                $selectedObjectsPreviews[] = '<div class="panel-body">';
                $selectedObjectsPreviews[] = $toolbar->render();
                $selectedObjectsPreviews[] = '</div>';
                $selectedObjectsPreviews[] = '</div>';

                $selectedObjectsPreview = implode(PHP_EOL, $selectedObjectsPreviews);

                $html = [];

                $html[] = $this->renderHeader();

                $parameters = [];
                $parameters[self::PARAM_CONTEXT] = Manager::CONTEXT;
                $parameters[self::PARAM_ACTION] = self::ACTION_CREATE;

                $url = $this->getUrlGenerator()->fromParameters($parameters);

                $html[] = '<div class="alert alert-info" role="alert">' .
                    $this->getTranslator()->trans('ShareInformation', ['WORKSPACE_URL' => $url], Manager::CONTEXT) .
                    '</div>';

                $html[] = $selectedObjectsPreview;
                $html[] = '<h3 style="margin-bottom: 30px;">' .
                    $this->getTranslator()->trans('ShareInWorkspaces', [], Manager::CONTEXT) . '</h3>';
                $html[] = $this->renderTable();
                $html[] = $this->renderFooter();

                return implode(PHP_EOL, $html);
            }
        }

        return '';
    }

    /**
     * @see \Chamilo\Core\Repository\Manager::getAdditionalParameters()
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }

    protected function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->getService(ContentObjectRelationService::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @return int[]
     */
    public function getSelectedContentObjectIdentifiers(): array
    {
        if (!isset($this->selectedContentObjectIdentifiers))
        {
            $this->selectedContentObjectIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID, []
            );
        }

        return $this->selectedContentObjectIdentifiers;
    }

    /**
     * @return int[]
     */
    public function getSelectedWorkspaceIdentifiers(): array
    {
        if (!isset($this->selectedWorkspaceIdentifiers))
        {
            $this->selectedWorkspaceIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(
                Manager::PARAM_SELECTED_WORKSPACE_ID, []
            );
        }

        return $this->selectedWorkspaceIdentifiers;
    }

    protected function getShareTableRenderer(): ShareTableRenderer
    {
        return $this->getService(ShareTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            $this->getContentObjectRelationService()->countAvailableWorkspacesForContentObjectIdentifiersAndUser(
                $this->getSelectedContentObjectIdentifiers(), $this->getUser()
            );
        $shareTableRenderer = $this->getShareTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $shareTableRenderer->getParameterNames(), $shareTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $workspaces =
            $this->getContentObjectRelationService()->getAvailableWorkspacesForContentObjectIdentifiersAndUser(
                $this->getSelectedContentObjectIdentifiers(), $this->getUser(),
                $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
                $shareTableRenderer->determineOrderBy($tableParameterValues)
            );

        return $shareTableRenderer->render($tableParameterValues, $workspaces);
    }
}
