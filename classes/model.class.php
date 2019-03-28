<?php
/**
 * Core Model Class
 */
class ModelCore extends CommonCore
{
    const MAIN_CONFIG_PATH = __DIR__.'/../../config/main.json';

    public $object        = null;
    public $voClassName   = null;
    public $form          = null;
    public $configData    = [];

    /**
     * summary
     */
    public function __construct()
    {
        $this->setConfigData();
    }

    /**
     * summary
     */
    public function setObject(string $objectClassName = '') : void
    {
        if ($this->object === null) {
            $this->object = new $objectClassName();
            $this->object->initStore();
        }
    }

    /**
     * summary
     */
    public function setValuesObjectClass(string $voClassName = '') : void
    {
        $this->voClassName = $voClassName;
    }

    /**
     * summary
     */
    public function setConfigData() : void
    {
        $configDataJSON = file_get_contents(self::MAIN_CONFIG_PATH);
        $this->configData = json_decode($configDataJSON, true);
    }

    /**
     * summary
     */
    public function getConfigValue(string $valueName = '') : string
    {
        if (isset($this->configData[$valueName])) {
            return (string) $this->configData[$valueName];
        }

        return NULL;
    }

    /**
     * summary
     */
    public function getConfigArrayValue(string $valueName = '') : array
    {
        if (isset($this->configData[$valueName])) {
            return (array) $this->configData[$valueName];
        }

        return [];
    }

    /**
     * summary
     */
    public function getVO(array $inputArray = []) : ValuesObject
    {
        if ($this->voClassName == NULL) {
            throw new Exception('Value Object class not set');
        }

        return new $this->voClassName($inputArray);
    }

    /**
     * summary
     */
    public function getVOArray(array $inputArrays = []) : array
    {
        foreach ($inputArrays as $inputArraysIDX => $inputArray) {
            $inputArrays[$inputArraysIDX] = $this->getVO($inputArray);
        }

        return $inputArrays;
    }

    public function getByID(int $id = -1) : ValuesObject
    {
        $values = $this->object->getByID(
            $this->object->getDefaultTableName(),
            $id
        );

        return $this->getVO($values);
    }

    public function getByPage(int $page = 1) : array
    {
        $values = $this->object->getAllByPage(
            $this->object->getDefaultTableName(),
            [],
            $page
        );

        return $this->getVOArray($values);
    }

    public function removeByID(int $id = -1) : bool
    {
        return $this->object->removeByID(
            $this->object->getDefaultTableName(),
            $id
        );
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