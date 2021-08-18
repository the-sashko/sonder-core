<?php

/**
 * Plugin For Generating Pagination Links
 */
class PaginatorPlugin
{
    /**
     * @var int Cout Of Pages
     */
    private $_pageCount = 1;

    /**
     * @var int Current Page
     */
    private $_currentPage = 1;

    /**
     * @var string|null URL Of Base Link
     */
    private $_link = null;

    /**
     * @var array List Of Pages
     */
    private $_pages = [];

    /**
     * Get HTML Of Pagination
     *
     * @param int|null $pageCount Cout Of Pages
     * @param int|null $currentPage Current Page
     * @param string|null $link URL Of Base Link
     *
     * @return string|null HTML Of Pagination
     */
    public function getPagination(
        ?int    $pageCount = null,
        ?int    $currentPage = null,
        ?string $link = null
    ): ?string
    {
        if (empty($pageCount) || $pageCount < 2 || empty($link)) {
            return null;
        }

        $this->_pageCount = $pageCount;
        $this->_currentPage = !empty($currentPage) ? $currentPage : 1;

        if ($this->_currentPage > $this->_pageCount) {
            return null;
        }

        $link = (string)$link;

        if (preg_match('/^(.*)\/page\-([0-9]+)\/$/su', $link)) {
            $link = preg_replace('/^(.*)\/page\-([0-9]+)\/$/su', '$1', $link);
        }

        if (preg_match('/^(.*)\/$/su', $link)) {
            $link = preg_replace('/^(.*)\/$/su', '$1', $link);
        }

        $this->_link = !empty($link) ? $link : '#';

        $this->_createPages();
        $this->_setPagesHtml();

        return $this->_getPaginatorHtml();
    }

    /**
     * @return bool
     */
    private function _createPages(): bool
    {
        if ($this->_pageCount == 2) {
            $this->_pages = [
                '1' => 1,
                '2' => 2
            ];

            return true;
        }

        $this->_pages[$this->_currentPage] = $this->_currentPage;

        if ($this->_pageCount > 9) {
            $this->_pages[4] = null;
            $this->_pages[$this->_pageCount - 4] = null;
        }

        if ($this->_currentPage > 1) {
            $this->_pages[$this->_currentPage - 1] = $this->_currentPage - 1;
        }

        if ($this->_currentPage > 5) {
            $this->_pages[$this->_currentPage - 2] = null;
        }

        if ($this->_currentPage < $this->_pageCount - 1) {
            $this->_pages[$this->_currentPage + 1] = $this->_currentPage + 1;
        }

        if ($this->_currentPage < $this->_pageCount - 4) {
            $this->_pages[$this->_currentPage + 2] = null;
        }

        for ($page = 1; $page <= 3; $page++) {
            $this->_pages[$page] = $page;
        }

        for (
            $page = $this->_pageCount - 2;
            $page <= $this->_pageCount;
            $page++
        ) {
            $this->_pages[$page] = $page;
        }

        $this->_pages[$this->_currentPage] = $this->_currentPage;

        ksort($this->_pages);

        for ($page = 1; $page <= $this->_pageCount; $page++) {
            if (
                empty($this->_pages[$page - 1]) &&
                empty($this->_pages[$page])
            ) {
                unset($this->_pages[$page]);
            }
        }

        return true;
    }

    /**
     * Convert List Of Page Into List Of HTML Tags
     */
    private function _setPagesHtml(): void
    {
        $prevPage = 1;

        foreach ($this->_pages as $key => $page) {
            if (empty($page)) {
                $this->_pages[$key] = '<span>...</span>';

                continue;
            }

            if ($page == $this->_currentPage) {
                $this->_pages[$key] = sprintf(
                    '<span>%d</span>',
                    $this->_currentPage
                );

                continue;
            }

            if ($page < 2) {
                $this->_pages[$key] = sprintf(
                    '<a href="%s/">%d</a>',
                    $this->_link,
                    $page
                );

                continue;
            }

            $this->_pages[$key] = sprintf(
                '<a href="%s/page-%d/">%d</a>',
                $this->_link,
                $page,
                $page
            );
        }
    }

    /**
     * Get HTML Of Pagination From List Of HTML Tags
     *
     * @return string|null HTML Of Pagination
     */
    private function _getPaginatorHtml(): ?string
    {
        $paginatorHtml = '';

        if (count($this->_pages) < 2) {
            return null;
        }

        foreach ($this->_pages as $page) {
            $paginatorHtml = $paginatorHtml . $page;
        }

        return $paginatorHtml;
    }
}
