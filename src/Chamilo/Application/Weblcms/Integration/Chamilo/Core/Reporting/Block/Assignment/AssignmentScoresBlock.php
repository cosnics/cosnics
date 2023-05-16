<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of scores of each assignment per
 *          user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 */
abstract class AssignmentScoresBlock extends AssignmentReportingManager
{

    /**
     * @var ReportingData
     */
    private $reporting_data;

    public function count_data()
    {
        if (!isset($this->reporting_data))
        {
            $this->reporting_data = new ReportingData();

            $course_id = $this->getCourseId();
            $entityType = $this->getAssignmentScoresEntityType();

            $publications = $this->retrieveAssignmentPublicationsForCourse(
                $course_id, $entityType
            );

            $publicationTitlesById = $this->determineReportingHeaders($publications);
            $entities = $this->retrieveEntitiesForCourse($course_id);

            foreach ($entities as $key => $entity)
            {
                $this->reporting_data->add_category($key);

                $this->reporting_data->add_data_category_row(
                    $key,
                    Translation::get('Name'),
                    $this->renderEntityName($entity)
                );

                $entityId = $this->getEntityIdFromEntity($entity);

                foreach ($publications as $publication)
                {
                    $publicationId = $publication[ContentObjectPublication::PROPERTY_ID];

                    $publicationObject = new ContentObjectPublication();
                    $publicationObject->setId($publicationId);

                    $title = $publicationTitlesById[$publicationId];

                    if (!$this->getAssignmentService()->countEntriesForContentObjectPublicationEntityTypeAndId(
                        $publicationObject, $entityType, $entityId
                    ))
                    {
                        $this->reporting_data->add_data_category_row($key, $title, null);
                        continue;
                    }

                    $lastScore = $this->getAssignmentService()->getLastScoreForContentObjectPublicationEntityTypeAndId(
                        $publicationObject, $entityType, $entityId
                    );

                    if ($lastScore)
                    {
                        $score = $this->format_score_html($lastScore);

                        $this->reporting_data->add_data_category_row($key, $title, $score);
                    }
                    else
                    {
                        $link = $this->getEntityUrl($course_id, $publicationId, $entityType, $entityId);

                        $this->reporting_data->add_data_category_row(
                            $key,
                            $title,
                            $this->createLink($link,'?', '_blank')
                        );
                    }
                }
            }

            $this->reporting_data->hide_categories();
        }

        return $this->reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    /**
     * @param $publications
     *
     * @return string[]
     */
    protected function determineReportingHeaders($publications)
    {
        $publicationTitlesById = [];

        // set the table headers
        $headings = [];
        $headings[] = Translation::get('Name');

        foreach ($publications as $publication)
        {
            $publicationId = $publication[ContentObjectPublication::PROPERTY_ID];

            if (count($publications) > 5)
            {
                $publicationTitlesById[$publicationId] = '<div id="' . $publicationId . '">' .
                    substr($publication[ContentObject::PROPERTY_TITLE], 0, 14) . '</div>';
            }
            else
            {
                $publicationTitlesById[$publicationId] = '<div id="' . $publicationId . '">' .
                    $publication[ContentObject::PROPERTY_TITLE] . '</div>';
            }

            $headings[] = $publicationTitlesById[$publicationId];
        }

        $this->reporting_data->set_rows($headings);

        return $publicationTitlesById;
    }

    /**
     * @return int
     */
    abstract protected function getAssignmentScoresEntityType();


    /**
     * @param mixed $entity
     *
     * @return string
     */
    abstract protected function renderEntityName($entity);

    /**
     * @param mixed $entity
     *
     * @return int
     */
    abstract protected function getEntityIdFromEntity($entity);

    /**
     * @param int $course_id
     *
     * @return mixed
     */
    abstract protected function retrieveEntitiesForCourse($course_id);
}
