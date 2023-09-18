<?php
namespace Chamilo\Application\Weblcms\Tool\Service;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;

/**
 * Service to generate breadcrumbs for the categories (of a publication) or a selected category
 *
 * @package Chamilo\Application\Weblcms\Tool\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CategoryBreadcrumbsGenerator
{
    /**
     * @var \Chamilo\Application\Weblcms\Service\PublicationService
     */
    protected $publicationService;

    /**
     * CategoryBreadcrumbsGenerator constructor.
     *
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     */
    public function __construct(PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    /**
     * Generates breadcrumbs recursively for a given ContentObjectPublicationCategory
     *
     * @param \Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail $breadcrumbTrail
     * @param Application $urlGenerator
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory $contentObjectPublicationCategory
     */
    public function generateBreadcrumbsForCategory(
        BreadcrumbTrail $breadcrumbTrail, Application $urlGenerator,
        ContentObjectPublicationCategory $contentObjectPublicationCategory = null
    )
    {
        if (!$contentObjectPublicationCategory instanceof ContentObjectPublicationCategory)
        {
            return;
        }

        $parentId = $contentObjectPublicationCategory->get_parent();

        if (!empty($parentId))
        {
            $parent = $this->publicationService->getPublicationCategoryById($parentId);
            $this->generateBreadcrumbsForCategory($breadcrumbTrail, $urlGenerator, $parent);
        }

        $this->generateBreadcrumbForCategory($breadcrumbTrail, $urlGenerator, $contentObjectPublicationCategory);
    }

    /**
     * Generates category breadcrumbs recursively for a given ContentObjectPublication
     *
     * @param \Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail $breadcrumbTrail
     * @param \Chamilo\Libraries\Architecture\Application\Application $urlGenerator
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    public function generateBreadcrumbsForContentObjectPublication(
        BreadcrumbTrail $breadcrumbTrail, Application $urlGenerator, ContentObjectPublication $contentObjectPublication
    )
    {
        $parentId = $contentObjectPublication->get_category_id();
        if(!empty($parentId))
        {
            $parent = $this->publicationService->getPublicationCategoryById($parentId);
            $this->generateBreadcrumbsForCategory($breadcrumbTrail, $urlGenerator, $parent);
        }
    }

    /**
     * Generates a single breadcrumb for a ContentObjectPublicationCategory
     *
     * @param $breadcrumbTrail
     * @param Application $urlGenerator
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory $contentObjectPublicationCategory
     */
    protected function generateBreadcrumbForCategory(
        BreadcrumbTrail $breadcrumbTrail, Application $urlGenerator,
        ContentObjectPublicationCategory $contentObjectPublicationCategory
    )
    {
        $breadcrumbTrail->add(
            new Breadcrumb(
                $urlGenerator->get_url(
                    [
                        Manager::PARAM_CATEGORY => $contentObjectPublicationCategory->getId(),
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE
                    ],
                    [
                        Manager::PARAM_PUBLICATION
                    ]
                ), $contentObjectPublicationCategory->get_name()
            )
        );
    }
}
