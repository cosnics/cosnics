<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component\AjaxComponent;

use Chamilo\Core\Repository\ContentObject\GradeBook\Service\GradeBookService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Service\GradeBookAjaxService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Service\ImportFromCSVService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;


/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_LOAD_GRADEBOOK_DATA = 'LoadGradeBookData';
    const ACTION_ADD_CATEGORY = 'AddCategory';
    const ACTION_UPDATE_CATEGORY = 'UpdateCategory';
    const ACTION_MOVE_CATEGORY = 'MoveCategory';
    const ACTION_REMOVE_CATEGORY = 'RemoveCategory';
    const ACTION_ADD_COLUMN = 'AddColumn';
    const ACTION_UPDATE_COLUMN = 'UpdateColumn';
    const ACTION_UPDATE_COLUMN_CATEGORY = 'UpdateColumnCategory';
    const ACTION_MOVE_COLUMN = 'MoveColumn';
    const ACTION_ADD_COLUMN_SUBITEM = 'AddColumnSubItem';
    const ACTION_REMOVE_COLUMN_SUBITEM = 'RemoveColumnSubItem';
    const ACTION_REMOVE_COLUMN = 'RemoveColumn';
    const ACTION_SYNCHRONIZE_GRADEBOOK = 'SynchronizeGradeBook';
    const ACTION_OVERWRITE_SCORE = 'OverwriteScore';
    const ACTION_REVERT_OVERWRITTEN_SCORE = 'RevertOverwrittenScore';
    const ACTION_UPDATE_SCORE_COMMENT = 'UpdateScoreComment';
    const ACTION_CALCULATE_TOTAL_SCORES = 'CalculateTotalScores';
    const ACTION_UPDATE_DISPLAY_TOTAL = 'UpdateDisplayTotal';
    const ACTION_PROCESS_CSV = 'ProcessCSV';
    const ACTION_IMPORT = 'Import';

    const PARAM_ACTION = 'gradebook_display_ajax_action';

    const PARAM_GRADEBOOK_DATA_ID = 'gradebookDataId';
    const PARAM_VERSION = 'version';
    const PARAM_CATEGORY_DATA = 'categoryData';
    const PARAM_CATEGORY_ID = 'categoryId';
    const PARAM_NEW_SORT = 'newSort';
    const PARAM_GRADECOLUMN_DATA = 'gradeColumnData';
    const PARAM_GRADECOLUMN_ID = 'gradeColumnId';
    const PARAM_GRADEITEM_ID = 'gradeItemId';
    const PARAM_GRADESCORE_ID = 'gradeScoreId';
    const PARAM_NEW_SCORE = 'newScore';
    const PARAM_NEW_SCORE_AUTH_ABSENT = 'newScoreAuthAbsent';
    const PARAM_SCORE_COMMENT = 'comment';
    const PARAM_IMPORT_TYPE = 'importType';
    const PARAM_IMPORT_SCORES = 'importScores';
    const PARAM_DISPLAY_TOTAL = 'displayTotal';

    /**
     * @var AjaxComponent
     */
    protected $ajaxComponent;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (!$applicationConfiguration->getApplication() instanceof AjaxComponent)
        {
            throw new \RuntimeException(
                'The ajax components from the gradebook display manager can only be called from ' .
                'within the AjaxComponent of the gradebook display application'
            );
        }

        $this->ajaxComponent = $applicationConfiguration->getApplication();
        if (!$this->ajaxComponent->getRightsService()->canUserEditGradeBook())
        {
            throw new NotAllowedException();
        }

        parent::__construct($applicationConfiguration);
    }

    /**
     * @return AjaxExceptionResponse|JsonResponse
     */
    function run()
    {
        try
        {
            $result = $this->runAjaxComponent();

            return new JsonResponse($this->serialize($result), 200, [], true);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);

            return new AjaxExceptionResponse($ex);
        }
    }

    /**
     * @return array
     */
    abstract function runAjaxComponent(): array;

    /**
     * @return GradeBookData
     *
     * @throws UserException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getGradeBookData(): GradeBookData
    {
        $gradeBook = $this->getGradeBook();
        $contextIdentifier = $this->getGradeBookServiceBridge()->getContextIdentifier();
        $gradeBookData = $this->getGradeBookService()->getGradeBookDataById($this->getGradeBookDataId(), $this->getVersion());

        $isContextValid = ($gradeBookData->getContentObjectId() == $gradeBook->getId()) &&
            ($gradeBookData->getContextClass() == $contextIdentifier->getContextClass()) &&
            ($gradeBookData->getContextId() == $contextIdentifier->getContextId());

        if (!$isContextValid)
        {
            throw new UserException('Invalid context for gradebook data with id ' . $this->getGradeBookDataId());
        }

        return $gradeBookData;
    }

    /**
     * @return bool
     */
    protected function canUserEditGradeBook(): bool
    {
        return $this->ajaxComponent->getRightsService()->canUserEditGradeBook();
    }

    /**
     * @return bool
     */
    protected function canUserViewGradeBook(): bool
    {
        return $this->ajaxComponent->getRightsService()->canUserEditGradeBook($this->getUser());
    }

    /**
     * @return GradeBookServiceBridgeInterface
     */
    protected function getGradeBookServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(GradeBookServiceBridgeInterface::class);
    }

    /**
     * @return int[]
     */
    protected function getTargetUserIds(): array
    {
        return $this->getGradeBookServiceBridge()->getTargetUserIds();
    }

    /**
     * @param string $json
     *
     * @return array
     */
    protected function deserialize(string $json): array
    {
        return $this->getSerializer()->deserialize($json, 'array', 'json');
    }

    /**
     * @param array $array
     *
     * @return string
     */
    protected function serialize(array $array): string
    {
        return $this->getSerializer()->serialize($array, 'json');
    }

    /**
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        if (empty($this->serializer))
        {
            $this->serializer = SerializerBuilder::create()
                ->setSerializationContextFactory(function () {
                    return SerializationContext::create()
                        ->setSerializeNull(true);
                })
                ->setDeserializationContextFactory(function () {
                    return DeserializationContext::create();
                })
                ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
                ->build();
        }
        return $this->serializer;
    }

    protected function get_root_content_object()
    {
        return $this->get_application()->get_root_content_object();
    }

    /**
     * @return GradeBookService
     */
    protected function getGradeBookService()
    {
        return $this->getService(GradeBookService::class);
    }

    /**
     * @return GradeBookAjaxService
     */
    protected function getGradeBookAjaxService()
    {
        return $this->getService(GradeBookAjaxService::class);
    }

    /**
     * @return ImportFromCSVService
     */
    protected function getImportFromCSVService()
    {
        return $this->getService(ImportFromCSVService::class);
    }

    /**
     * @return GradeBook
     * @throws UserException
     */
    protected function getGradeBook(): GradeBook
    {
        $gradebook = $this->get_root_content_object();

        if (!$gradebook instanceof GradeBook)
        {
            $this->throwUserException('GradeBookNotFound');
        }

        return $gradebook;
    }

    protected function getFilterParametersBuilder() : FilterParametersBuilder
    {
        return $this->getService(FilterParametersBuilder::class);
    }

    /**
     * @throws UserException
     */
    protected function throwUserException(string $key)
    {
        $this->ajaxComponent->throwUserException($key);
    }

    /**
     * @return int
     */
    protected function getGradeBookDataId()
    {
        return (int) $this->getRequest()->getFromPost(self::PARAM_GRADEBOOK_DATA_ID);
    }

    /**
     * @return int
     */
    protected function getVersion()
    {
        return (int) $this->getRequest()->getFromPost(self::PARAM_VERSION);
    }

    /**
     * @return string
     */
    protected function getCategoryData()
    {
        return $this->getRequest()->getFromPost(self::PARAM_CATEGORY_DATA);
    }

    /**
     * @return string
     */
    protected function getGradeColumnData()
    {
        return $this->getRequest()->getFromPost(self::PARAM_GRADECOLUMN_DATA);
    }

    /**
     * @return int
     */
    protected function getGradeItemId()
    {
        return (int) $this->getRequest()->getFromPostOrUrl(self::PARAM_GRADEITEM_ID);
    }

    /**
     * @return int
     */
    protected function getGradeColumnId(): int
    {
        return (int) $this->getRequest()->getFromPost(self::PARAM_GRADECOLUMN_ID);
    }

    /**
     * @return int
     */
    protected function getGradeScoreId(): int
    {
        return (int) $this->getRequest()->getFromPost(self::PARAM_GRADESCORE_ID);
    }

    /**
     * @return int|null
     */
    protected function getCategoryId(): ?int
    {
        $id = $this->getRequest()->getFromPost(self::PARAM_CATEGORY_ID);
        return $id == 'null' ? null : (int) $id;
    }

    /**
     * @return float|null
     */
    protected function getNewScore(): ?float
    {
        $score = $this->getRequest()->getFromPost(self::PARAM_NEW_SCORE);
        return $score == 'null' ? null : (float) $score;
    }

    /**
     * @return bool
     */
    protected function getNewScoreAuthAbsent(): bool
    {
        return $this->getRequest()->getFromPost(self::PARAM_NEW_SCORE_AUTH_ABSENT) == 'true';
    }

    /**
     * @return string|null
     */
    protected function getScoreComment(): ?string
    {
        $comment = $this->getRequest()->getFromPost(self::PARAM_SCORE_COMMENT);
        return ($comment == 'null' || $comment == '') ? null : $comment;
    }

    /**
     * @return string
     */
    protected function getImportType(): string
    {
        $importType = $this->getRequest()->getFromPost(self::PARAM_IMPORT_TYPE);
        return $importType == ImportFromCSVService::TYPE_SCORES_COMMENTS ? $importType : ImportFromCSVService::TYPE_SCORES;
    }

    /**
     * @return string
     */
    protected function getImportScores()
    {
        return $this->getRequest()->getFromPost(self::PARAM_IMPORT_SCORES);
    }

    /**
     * @return int|null
     */
    protected function getDisplayTotal(): ?int
    {
        $id = $this->getRequest()->getFromPost(self::PARAM_DISPLAY_TOTAL);
        return $id == 'null' ? null : (int) $id;
    }
}