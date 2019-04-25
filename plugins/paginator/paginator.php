<?php
/**
 * Plugin For Generating Pagination Links
 */
class PaginatorPlugin
{
    /**
     * @var int Cout Of Pages
     */
    public $pageCount = 1;

    /**
     * @var int Current Page
     */
    public $currPage = 1;

    /**
     * @var string URL Of Base Link
     */
    public $link = '';

    /**
     * @var array List Of Pages
     */
    public $pages = [];

    /**
     * Get HTML Of Pagination
     *
     * @param int    $pageCount Cout Of Pages
     * @param int    $currPage  Current Page
     * @param string $link      URL Of Base Link
     *
     * @return string HTML Of Pagination
     */
    public function getHTML(
        int    $pageCount = 1,
        int    $currPage  = 1,
        string $link      = ''
    ) : string
    {
        $this->pageCount = $pageCount;
        $this->currPage  = $currPage;

        if (preg_match('/^(.*)\/$/su', $link)) {
            $link = preg_replace('/^(.*)\/$/su', '$1', $link);
        }

        $this->link = $link;

        $this->_filterPages();
        $this->_setPagesHTML();

        return $this->_getPaginatorHTML();
    }

    /**
     * Remove Extra Pages From List
     */
    private function _filterPages() : void
    {
        for ($page = 1; $page <= $this->pageCount; $page++) {
            if (
                ($page > 3 && $page < $this->currPage - 1) ||
                ($page < $this->pageCount - 2 && $page > $this->currPage + 1)
            ) {
                $page = -1;
            }

            $this->pages[] = $page;
        }
    }

    /**
     * Convert List Of Page Into List Of HTML Tags
     */
    private function _setPagesHTML() : void
    {
        $prevPage = 1;
        foreach ($this->pages as $idx => $page) {
            if ($page > 0) {
                if ($this->pages[$idx] !== $this->currPage) {
                    if ($page > 1) {
                        $this->pages[$idx] = '<a href="'.
                                       $this->link.'/page-'.$page.'/>'.
                                       $page.'</a>';

                        $prevPage = $page;

                        continue;
                    }

                    $this->pages[$idx] = '<a href="'.$this->link.'/>1</a>';

                    $prevPage = $page;

                    continue;
                }

                $this->pages[$idx] = '<span>'.$page.'</span>';

                continue;
            }

            if ($prevPage < 1) {
                unset($this->pages[$idx]);

                continue;
            }

            $this->pages[$idx] = '<span>...</span>';
            $prevPage          = $page;
        }
    }

    /**
     * Get HTML Of Pagination From List Of HTML Tags
     *
     * @return string HTML Of Pagination
     */
    private function _getPaginatorHTML() : string
    {
        $paginatorHTML = '';

        if (count($this->pages) < 2) {
            return '';
        }

        foreach ($this->pages as $idx => $page) {
            $paginatorHTML = $paginatorHTML.$page;
        }

        return $paginatorHTML;
    }
}
?>
