<?php

namespace Chamilo\Core\Repository\DTO;

class CategoryTreeFormData
{
    /**
     * @var int
     */
    protected $categoryId;

    /**
     * @var string
     */
    protected $categoryTitle;

    /**
     * CategoryTreeFormData constructor.
     *
     * @param int $categoryId
     * @param string $categoryTitle
     */
    public function __construct(int $categoryId, string $categoryTitle)
    {
        $this->categoryId = $categoryId;
        $this->categoryTitle = $categoryTitle;
    }

    /**
     * @return int
     */
    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getCategoryTitle(): ?string
    {
        return $this->categoryTitle;
    }
}
