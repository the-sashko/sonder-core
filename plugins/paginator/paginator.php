<?php

/**
 * 
 */
class PaginatorPlugin
{
    public $pageCount = 1;
    public $currPage = 1;
    public $link = '';
    public $pages = [];

    public function getHTML(
        int $pageCount = 1,
        int $currPage = 1,
        string $link = ''
    ) : string
    {
        $this->pageCount = $pageCount;
        $this->currPage = $currPage;

        if (preg_match('/^(.*)\/$/su', $link)) {
            $link = preg_replace('/^(.*)\/$/su', '$1', $link);
        }

        $this->link = $link;

        $this->_filterPages();
        $this->_setPagesHTML();
        return $this->_getPaginatorHTML();
    }

    private function _filterPages() : void
    {
        for ($page = 1; $page <= $this->pageCount; $page++) {
            if (
                ($page > 3 && $page < $this->currPage - 1) || 
                ($page < $this->pageCount - 2 && $page > $this->currPage + 1)
            ) {
                $this->pages[] = -1;
            } else {
                $this->pages[] = $page;
            }
        }
    }

    private function _setPagesHTML() : void
    {
        $prevPage = 1;
        foreach ($this->pages AS $idx => $page) {
            if ($page > 0) {
                if ($this->pages[$idx] != $this->currPage) {
                    if ($page > 1) {
                        $this->pages[$idx] = '<a href="'.
                                       $this->link.'/page-'.$page.'/>'.
                                       $page.'</a>';
                    } else {
                        $this->pages[$idx] = '<a href="'.$this->link.'/>1</a>';
                    }
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
            $prevPage = $page;
        }
    }

    private function _getPaginatorHTML() : string
    {
        $paginatorHTML = '';

        if (count($this->pages) < 2) {
            return '';
        }

        foreach ($this->pages AS $idx => $page) {
            $paginatorHTML = $paginatorHTML.$page;
        }

        return $paginatorHTML;
    }
}
?>