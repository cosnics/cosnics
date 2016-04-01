<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\File\Properties\FileProperties;

/**
 *
 * @package Chamilo\Core\Repository\Common\Import
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectImportService
{
    const PARAM_IMPORT_TYPE = 'import_type';

    /**
     *
     * @var \Chamilo\Core\Repository\Form\ContentObjectImportForm
     */
    private $form;

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspace;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var integer[]
     */
    private $contentObjectIds;

    /**
     *
     * @param string $type
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct($type, WorkspaceInterface $workspace, Application $application)
    {
        $this->type = $type;
        $this->workspace = $workspace;
        $this->application = $application;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     */
    public function setWorkspace(WorkspaceInterface $workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Form\ContentObjectImportForm
     */
    public function getForm()
    {
        if (! isset($this->form))
        {
            $this->form = ContentObjectImportForm :: factory(
                $this->getType(),
                $this->getWorkspace(),
                $this->getApplication(),
                'post',
                $this->getApplication()->get_url(array(self :: PARAM_IMPORT_TYPE => $this->getType())));
        }

        return $this->form;
    }

    /**
     *
     * @return integer[]
     */
    public function getContentObjectIds()
    {
        return $this->contentObjectIds;
    }

    public function hasFinished()
    {
        if ($this->getForm()->validate())
        {
            $values = $this->getForm()->exportValues();
            $parent_id = $values[ContentObject :: PROPERTY_PARENT_ID];
            $new_category_name = $values[ContentObjectImportForm :: NEW_CATEGORY];

            if (! StringUtilities :: getInstance()->isNullOrEmpty($new_category_name, true))
            {
                $new_category = new RepositoryCategory();
                $new_category->set_name($new_category_name);
                $new_category->set_parent($parent_id);
                $new_category->set_type_id($this->getWorkspace()->getId());
                $new_category->set_type($this->getWorkspace()->getWorkspaceType());

                if (! $new_category->create())
                {
                    throw new \Exception(Translation :: get('CategoryCreationFailed'));
                }
                else
                {
                    $category_id = $new_category->get_id();
                }
            }
            else
            {
                $category_id = $parent_id;
            }

            if (isset($_FILES[ContentObjectImportForm :: IMPORT_FILE_NAME]))
            {
                $file = FileProperties :: from_upload($_FILES[ContentObjectImportForm :: IMPORT_FILE_NAME]);
            }
            else
            {
                $file = null;
            }

            $parameters = ImportParameters :: factory(
                $this->getForm()->exportValue(ContentObjectImportForm :: PROPERTY_TYPE),
                $this->getApplication()->getUser()->getId(),
                $this->getWorkspace(),
                $category_id,
                $file,
                $values);

            $controller = ContentObjectImportController :: factory($parameters);
            $this->contentObjectIds = $controller->run();

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @return string
     */
    public function renderForm()
    {
        return $this->getForm()->toHtml();
    }

    public function renderTypeSelector($availableTypes = array())
    {
        $html = array();

        $html[] = '<div class="btn-group">';

        foreach ($availableTypes as $type => $name)
        {
            $imageContext = \Chamilo\Core\Repository\Manager :: package();

            $html[] = '<a class="btn btn-default" href="' .
                 $this->getApplication()->get_url(array(self :: PARAM_IMPORT_TYPE => $type)) . '">';
            $html[] = '<img src="' . Theme :: getInstance()->getImagePath($imageContext, 'Import/' . $type) . '" /> ';
            $html[] = $name;
            $html[] = '</a>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}