<?php


namespace dollmetzer\zzaplib\data;


class Pagination
{

    /**
     * @var int Max number of entries per Page
     */
    protected $pageLength;

    /**
     * @var int Current Page number
     */
    protected $page;

    /**
     * @var int Highest page number
     */
    protected $pageMax;

    /**
     * @var int Number of page indices to display left and right from the current page index
     */
    protected $displayWidth;

    /**
     * @var int Smallest page number to display
     */
    protected $displayFirst;

    /**
     * @var int Highest page number to display
     */
    protected $displayLast;

    /**
     * @var int
     */
    protected $entryFirst;

    /**
     * @var int
     */
    protected $entryLength;

    /**
     * Pagination constructor.
     *
     * @param int $page Current Page number
     * @param int $pageLength Max number of entries per Page
     * @param int $displayWidth Number of page indices to display left and right from the current page index
     */
    public function __construct(int $page=0, int $pageLength=10, int $displayWidth=4)
    {
        $this->page = $page;
        $this->pageLength = $pageLength;
        $this->displayWidth = $displayWidth;
        $this->entryLength = 0;
        $this->calculate(0);
    }

    /**
     * Calculate number of pages, first page in display, last page in display, ...
     *
     * @param int $entryLength Total number of entries in the list
     */
    public function calculate(int $entryLength)
    {

        $this->entryLength = $entryLength;
        $this->pageMax = ceil($entryLength / $this->pageLength);

        $this->displayFirst = $this->page - $this->displayWidth;
        if($this->displayFirst < 0) {
            $this->displayFirst = 0;
        }
        $this->displayLast = $this->page + $this->displayWidth;
        if($this->displayLast > $this->pageMax) {
            $this->displayLast = $this->pageMax -1;
        }
        if($this->displayLast < 0) {
            $this->displayLast = 0;
        }
        $this->entryFirst = $this->page * $this->pageLength;

    }

    /**
     * @return int
     */
    public function getPageLength(): int
    {
        return $this->pageLength;
    }

    /**
     * @param int $pageLength
     */
    public function setPageLength(int $pageLength)
    {
        $this->pageLength = $pageLength;
        $this->calculate($this->entryLength);
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
        $this->calculate($this->entryLength);
    }

    /**
     * @return int
     */
    public function getPageMax(): int
    {
        return $this->pageMax;
    }

    /**
     * @return int
     */
    public function getDisplayWidth(): int
    {
        return $this->displayWidth;
    }

    /**
     * @param int $displayWidth
     */
    public function setDisplayWidth(int $displayWidth)
    {
        $this->displayWidth = $displayWidth;
        $this->calculate($this->entryLength);
    }

    /**
     * @return int
     */
    public function getDisplayFirst(): int
    {
        return $this->displayFirst;
    }

    /**
     * @return int
     */
    public function getDisplayLast(): int
    {
        return $this->displayLast;
    }

    /**
     * @return mixed
     */
    public function getEntryFirst()
    {
        return $this->entryFirst;
    }

}