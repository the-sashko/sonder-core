<?php

namespace Sonder\Plugins;

final class PaginatorPlugin
{
    /**
     * @var int
     */
    private int $_pageCount = 1;

    /**
     * @var int
     */
    private int $_currentPage = 1;

    /**
     * @var string|null
     */
    private ?string $_link = null;

    /**
     * @var array
     */
    private array $_pages = [];

    /**
     * @param int|null $pageCount
     * @param int|null $currentPage
     * @param string|null $link
     *
     * @return string|null
     */
    final public function getPagination(
        ?int    $pageCount = null,
        ?int    $currentPage = null,
        ?string $link = null
    ): ?string {
        if (empty($pageCount) || $pageCount < 2 || empty($link)) {
            return null;
        }

        $this->_pageCount = $pageCount;
        $this->_currentPage = !empty($currentPage) ? $currentPage : 1;

        if ($this->_currentPage > $this->_pageCount) {
            return null;
        }

        if (preg_match('/^(.*)\/page-([0-9]+)\/$/su', $link)) {
            $link = preg_replace('/^(.*)\/page-([0-9]+)\/$/su', '$1', $link);
        }

        if (preg_match('/^(.*)\/$/su', $link)) {
            $link = preg_replace('/^(.*)\/$/su', '$1', $link);
        }

        $this->_link = !empty($link) ? $link : '';

        if ($this->_pageCount == 2) {
            $this->_pages = [
                '1' => 1,
                '2' => 2
            ];
        }

        if ($this->_pageCount > 2) {
            $this->_createPages();
        }

        $this->_setPagesHtml();

        return $this->_getPaginatorHtml();
    }

    private function _createPages(): void
    {
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
            $page = $this->_pageCount - 2; $page <= $this->_pageCount; $page++
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
    }

    private function _setPagesHtml(): void
    {
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
     * @return string|null
     */
    private function _getPaginatorHtml(): ?string
    {

        if (count($this->_pages) < 2) {
            return null;
        }

        return implode('', $this->_pages);
    }
}
