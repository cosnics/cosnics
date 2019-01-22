<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin;

/**
 * This class describes all the possible settings for a similarity report
 *
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SimilarityReportSettings
{
    const SEARCH_REPOSITORY_INTERNET = 'INTERNET';
    const SEARCH_REPOSITORY_SUBMITTED_WORK = 'SUBMITTED_WORK';
    const SEARCH_REPOSITORY_PUBLICATION = 'PUBLICATION';
    const SEARCH_REPOSITORY_CROSSREF = 'CROSSREF';
    const SEARCH_REPOSITORY_CROSSREF_POSTED_CONTENT = 'CROSSREF_POSTED_CONTENT';

    const AUTO_EXCLUDE_NONE = 'NONE';
    const AUTO_EXCLUDE_ALL = 'ALL';
    const AUTO_EXCLUDE_GROUP = 'GROUP';
    const AUTO_EXCLUDE_GROUP_CONTEXT = 'GROUP_CONTEXT';

    const PRIORITY_HIGH = 'HIGH';
    const PRIORITY_LOW = 'LOW';

    /**
     * @var bool
     */
    protected $addToIndex = true;

    /**
     * @var array
     */
    protected $searchRepositories = [];

    /**
     * @var array
     */
    protected $submissionIdsToExclude = [];

    /**
     * @var string
     */
    protected $autoExcludeMatchingScope = self::AUTO_EXCLUDE_NONE;

    /**
     * @var string
     */
    protected $priority = self::PRIORITY_LOW;

    /**
     * @var bool
     */
    protected $excludeQuotes = false;

    /**
     * @var bool
     */
    protected $excludeBibliography = false;

    /**
     * @var bool
     */
    protected $excludeAbstract = false;

    /**
     * @var bool
     */
    protected $excludeMethods = false;

    /**
     * @var int
     */
    protected $excludeSmallMatches = 8;

    /**
     * @var bool
     */
    protected $excludeInternet = false;

    /**
     * @var bool
     */
    protected $excludePublications = false;

    /**
     * @var bool
     */
    protected $excludeSubmittedWorks = false;

    /**
     * @var bool
     */
    protected $excludeCrossref = false;

    /**
     * @var bool
     */
    protected $excludeCrossrefPostedContent = false;

    /**
     * @param bool $addToIndex
     */
    public function setAddToIndex(bool $addToIndex)
    {
        $this->addToIndex = $addToIndex;
    }

    /**
     * A list of search repositories to compare against
     *
     * @param array $searchRepositories
     */
    public function setSearchRepositories(array $searchRepositories)
    {
        foreach ($searchRepositories as $searchRepository)
        {
            if (!in_array($searchRepository, $this->getAllowedSearchRepositories()))
            {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The given search repository %s is not in the list of allowed search repositories (%s)',
                        $searchRepository, implode(', ', $this->getAllowedSearchRepositories())
                    )
                );
            }
        }

        $this->searchRepositories = $searchRepositories;
    }

    /**
     * @param array $submissionIdsToExclude
     */
    public function setSubmissionIdsToExclude(array $submissionIdsToExclude)
    {
        $this->submissionIdsToExclude = $submissionIdsToExclude;
    }

    /**
     * @param string $autoExcludeMatchingScope
     */
    public function setAutoExcludeMatchingScope(string $autoExcludeMatchingScope)
    {
        if (!in_array($autoExcludeMatchingScope, $this->getAllowedAutoExcludeMatchingScopes()))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given auto exclude matching scope %s is not in the list of allowed scopes (%s)',
                    $autoExcludeMatchingScope, implode(', ', $this->getAllowedAutoExcludeMatchingScopes())
                )
            );
        }

        $this->autoExcludeMatchingScope = $autoExcludeMatchingScope;
    }

    /**
     * @param string $priority
     */
    public function setPriority(string $priority)
    {
        if (!in_array($priority, $this->getAllowedPriorities()))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given auto exclude matching scope %s is not in the list of allowed scopes (%s)',
                    $priority, implode(', ', $this->getAllowedPriorities())
                )
            );
        }

        $this->priority = $priority;
    }

    /**
     * If set to true, text in quotes will not count as similar content
     *
     * @param bool $excludeQuotes
     */
    public function setExcludeQuotes(bool $excludeQuotes)
    {
        $this->excludeQuotes = $excludeQuotes;
    }

    /**
     * If set to true, text in a bibliography section will not count as similar content
     *
     * @param bool $excludeBibliography
     */
    public function setExcludeBibliography(bool $excludeBibliography)
    {
        $this->excludeBibliography = $excludeBibliography;
    }

    /**
     * If set to true, text in the abstract section of the submission will not count as similar content
     *
     * @param bool $excludeAbstract
     */
    public function setExcludeAbstract(bool $excludeAbstract)
    {
        $this->excludeAbstract = $excludeAbstract;
    }

    /**
     * If set to true, text in the method section of the submission will not count as similar content
     *
     * @param bool $excludeMethods
     */
    public function setExcludeMethods(bool $excludeMethods)
    {
        $this->excludeMethods = $excludeMethods;
    }

    /**
     * If set, similarity matches that match less than the specified amount of words will not count as similar content
     *
     * @param int $excludeSmallMatches
     */
    public function setExcludeSmallMatches(int $excludeSmallMatches)
    {
        $this->excludeSmallMatches = $excludeSmallMatches;
    }

    /**
     * If set to true, text matched to the internet collection will not count as similar content
     *
     * @param bool $excludeInternet
     */
    public function setExcludeInternet(bool $excludeInternet)
    {
        $this->excludeInternet = $excludeInternet;
    }

    /**
     * If set to true, text matched to the publications collection will not count as similar content
     *
     * @param bool $excludePublications
     */
    public function setExcludePublications(bool $excludePublications)
    {
        $this->excludePublications = $excludePublications;
    }

    /**
     * If set to true, text matched to the submitted works collection will not count as similar content
     *
     * @param bool $excludeSubmittedWorks
     */
    public function setExcludeSubmittedWorks(bool $excludeSubmittedWorks)
    {
        $this->excludeSubmittedWorks = $excludeSubmittedWorks;
    }

    /**
     * @param bool $excludeCrossref
     */
    public function setExcludeCrossref(bool $excludeCrossref)
    {
        $this->excludeCrossref = $excludeCrossref;
    }

    /**
     * @param bool $excludeCrossrefPostedContent
     */
    public function setExcludeCrossrefPostedContent(bool $excludeCrossrefPostedContent)
    {
        $this->excludeCrossrefPostedContent = $excludeCrossrefPostedContent;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if(empty($this->searchRepositories))
        {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'indexing_settings' => [
                'add_to_index' => $this->addToIndex
            ],
            'generation_settings' => [
                'search_repositories' => $this->searchRepositories,
                'submission_auto_excludes' => $this->submissionIdsToExclude,
                'auto_exclude_self_matching_scope' => $this->autoExcludeMatchingScope,
                'priority' => $this->priority
            ],
            'view_settings' => [
                'exclude_quotes' => $this->excludeQuotes,
                'exclude_bibliography' => $this->excludeBibliography,
                'exclude_abstract' => $this->excludeAbstract,
                'exclude_methods' => $this->excludeMethods,
                'exclude_small_matches' => $this->excludeSmallMatches,
                'exclude_internet' => $this->excludeInternet,
                'exclude_publications' => $this->excludePublications,
                'exclude_submitted_works' => $this->excludeSubmittedWorks,
                'exclude_crossref' => $this->excludeCrossref,
                'exclude_crossref_posted_content' => $this->excludeCrossrefPostedContent
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getAllowedSearchRepositories()
    {
        return [
            self::SEARCH_REPOSITORY_INTERNET, self::SEARCH_REPOSITORY_SUBMITTED_WORK,
            self::SEARCH_REPOSITORY_PUBLICATION, self::SEARCH_REPOSITORY_CROSSREF,
            self::SEARCH_REPOSITORY_CROSSREF_POSTED_CONTENT
        ];
    }

    /**
     * @return array
     */
    protected function getAllowedAutoExcludeMatchingScopes()
    {
        return [
            self::AUTO_EXCLUDE_NONE, self::AUTO_EXCLUDE_ALL, self::AUTO_EXCLUDE_GROUP, self::AUTO_EXCLUDE_GROUP_CONTEXT
        ];
    }

    /**
     * @return array
     */
    protected function getAllowedPriorities()
    {
        return [
            self::PRIORITY_HIGH, self::PRIORITY_LOW
        ];
    }
}