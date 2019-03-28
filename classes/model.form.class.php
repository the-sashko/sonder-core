<?php
/**
 * Core Model Form Class
 */
class ModelFormCore
{
    public $model = NULL;

    /**
     * summary
     */
    public function __construct(Object $model = NULL) {
        $this->model = $model;
    }

    /**
     * summary
     */
    public function getSlug(string $input = '', int $id = -1) : string
    {
        $translit  = $this->initPlugin('translit');
        $slug = $translit->getSlug($input);
        return $this->_getUniqSlug($slug, $id);
    }

    /**
     * summary
     */
    protected function _getUniqSlug(string $slug = '', int $id = -1) : string
    {
        $condition = "\"slug\" = '{$slug}' AND \"id\" != {$id}";

        $value = $this->object->getOneByCondition(
            $this->object->getDefaultTableName(),
            [],
            $condition
        );

        if (!count($value) > 0) {
            return $slug;
        }

        if (preg_match('/^(.*?)\-([0-9]+)$/su', $slug)) {
            $slugNumber = (int) preg_replace(
                '/^(.*?)\-([0-9]+)/su',
                '$2',
                $slug
            );
            $slugNumber++;
            $slug = preg_replace('/^(.*?)\-([0-9]+)/su', '$1', $slug);
            $slug = $slug.'-'.$slugNumber;
        } else {
            $slug = $slug.'-1';
        }

        return $this->_getUniqSlug($slug, $id);
    }

    public function formatTitle(string $title = '') : string
    {
        $title = preg_replace('/\s+/su', ' ', $title);
        $title = preg_replace('/(^\s)|(\s$)/su', '', $title);

        return $title;
    }

    public function formatText(string $text = '') : string
    {
        $text = preg_replace('/\n+/su', "\n", $text);
        $text = preg_replace('/\n+/su', '<br>', $text);
        $text = preg_replace('/\s+/su', ' ', $text);
        $text = preg_replace('/(\<br\>\s)|(\s\<br\>)/su', '<br>', $text);
        $text = preg_replace('/\<br\>/su', "\n", $text);
        $text = preg_replace('/\n+/su', "\n", $text);
        $text = preg_replace('/(^\s)|(\s$)/su', '', $text);

        return $text;
    }

    public function formatEmail(string $email = '') : string
    {
        $email = preg_replace('/\s+/su', '', $email);

        return $email;
    }

    public function formatSlug(string $slug = '') : string
    {
        $slug = preg_replace('/\s+/su', '', $slug);
        $slug = mb_convert_case($slug, MB_CASE_LOWER);

        return $slug;
    }

    public function formatURL(string $url = '') : string
    {
        $url = preg_replace('/\s+/su', '', $url);

        if (!preg_match('/^((http)|(https))\:\/\/(.*?)$/su', $url)) {
            $url = 'http://'.$url;
        }

        if (strlen($url) < 10) {
            $url = '';
        }

        return $url;
    }
}
?>