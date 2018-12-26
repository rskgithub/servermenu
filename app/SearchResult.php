<?php

namespace ServerMenu;


class SearchResult
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $link;
    /**
     * @var string
     */
    private $subtitle;
    /**
     * @var string
     */
    private $size;
    /**
     * @var string
     */
    private $date;
    /**
     * @var array
     */
    private $actions;

    /**
     * SearchResult constructor.
     * @param string $title
     * @param string $link
     * @param string $subtitle
     * @param string $size
     * @param string $date
     * @param array $actions
     */
    public function __construct(
        $title,
        $link,
        $subtitle,
        $size,
        $date,
        $actions = []
    )
    {
        $this->title = $title;
        $this->link = $link;
        $this->subtitle = $subtitle;
        $this->size = $size;
        $this->date = $date;
        $this->actions = $actions;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }
}
