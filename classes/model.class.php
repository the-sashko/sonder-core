<?php
/**
 * Core Model Class
 */
class ModelCore extends CommonCore
{
    /**
     * @var string Path To Main Config File
     */
    const MAIN_CONFIG_PATH = __DIR__.'/../../config/main.json';

    /**
     * @var object Model Object Class Instance
     */
    public $object = NULL;

    /**
     * @var string Model Values Object Class Name
     */
    public $voClassName = NULL;

    /**
     * @var array Data From JSON Config Files
     */
    public $configData = [];

    public function __construct()
    {
        parent::__construct();

        $this->setConfigData();
    }

    /**
     * Set Model Object Class Instance
     *
     * @param string Model Object Class Name
     */
    public function setObject(string $objectClassName = '') : void
    {
        if (NULL === $this->object) {
            $this->object = new $objectClassName();
            $this->object->initStore();
        }
    }

    /**
     * Set Model Value Object Class Name
     *
     * @param string Model Value Object Class Name
     */
    public function setValuesObjectClass(string $voClassName = '') : void
    {
        $this->voClassName = $voClassName;
    }

    /**
     * Set Config Main Data
     */
    public function setConfigData() : void
    {
        $configDataJSON   = file_get_contents(self::MAIN_CONFIG_PATH);
        $this->configData = json_decode($configDataJSON, TRUE);
    }

    /**
     * Get String Value From Main Config Data
     *
     * @param string $valueName Value Name
     *
     * @return string Value Data
     */
    public function getConfigValue(string $valueName = '') : string
    {
        if (isset($this->configData[$valueName])) {
            return (string) $this->configData[$valueName];
        }

        return NULL;
    }

    /**
     * Get Array Value From Main Config Data
     *
     * @param string $valueName Value Name
     *
     * @return array Value Data
     */
    public function getConfigArrayValue(string $valueName = '') : array
    {
        if (isset($this->configData[$valueName])) {
            return (array) $this->configData[$valueName];
        }

        return [];
    }

    /**
     * Get Model Value Object Instance
     *
     * @param array $inputArray List Of Values
     *
     * @return ValuesObject Values Object Instance
     */
    public function getVO(array $inputArray = []) : ValuesObject
    {
        if (NULL === $this->voClassName) {
            throw new Exception('Value Object class not set');
        }

        return new $this->voClassName($inputArray);
    }

    /**
     * Get List Of Model Value Object Instances
     *
     * @param array $inputArray List Of Values
     *
     * @return array List Of Model Value Object Instances
     */
    public function getVOArray(array $inputArrays = []) : array
    {
        foreach ($inputArrays as $inputArraysIDX => $inputArray) {
            $inputArrays[$inputArraysIDX] = $this->getVO($inputArray);
        }

        return $inputArrays;
    }

    /**
     * Get Model Value Object Instance By ID Value
     *
     * @param int $id ID Value
     *
     * @return ValuesObject Values Object Instance
     */
    public function getByID(int $id = -1) : ValuesObject
    {
        $values = $this->object->getByID(
            $this->object->getDefaultTableName(),
            $id
        );

        return $this->getVO($values);
    }

    /**
     * Get Model Value Object Instance By Slug Value
     *
     * @param string $slug Slug Value
     *
     * @return ValuesObject Values Object Instance
     */
    public function getBySlug(string $slug = '') : ValuesObject
    {
        $values = $this->object->getBySlug(
            $this->object->getDefaultTableName(),
            $slug
        );

        return $this->getVO($values);
    }

    /**
     * Get List Of Model Value Object Instances By Page Number
     *
     * @param int $page Page Number
     *
     * @return array List Of Model Value Object Instances
     */
    public function getByPage(int $page = 1) : array
    {
        $values = $this->object->getAllByPage(
            $this->object->getDefaultTableName(),
            [],
            $page
        );

        return $this->getVOArray($values);
    }

    /**
     * Remove Model Data From Data Base By ID Value
     *
     * @param int $id ID Value
     *
     * @return bool Is Data From Data Base Successffuly Removed
     */
    public function removeByID(int $id = -1) : bool
    {
        return $this->object->removeByID(
            $this->object->getDefaultTableName(),
            $id
        );
    }

    /**
     * Get Slug Value From String And Model Value ID
     *
     * @param string $input Input String
     * @param int    $id    ID Value
     *
     * @return string Output Slug Value
     */
    public function getSlug(string $input = '', int $id = -1) : string
    {
        $translit = $this->getPlugin('translit');
        $slug     = $translit->getSlug($input);

        return $this->_getUniqSlug($slug, $id);
    }

    /**
     * Get Unique Slug Value From Slug And Model Value ID
     *
     * @param string $input Input Slug Value
     * @param int    $id    ID Value
     *
     * @return string Output Slug Value
     */
    private function _getUniqSlug(
        string $slug = '',
        int    $id   = -1
    ) : string
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

            return $this->_getUniqSlug($slug, $id);
        }

        return $this->_getUniqSlug($slug.'-1', $id);
    }

    /**
     * Format Title Value
     *
     * @param string $title Input Title Value
     *
     * @return string Output Title Value
     */
    public function formatTitle(string $title = '') : string
    {
        $title = preg_replace('/\s+/su', ' ', $title);
        $title = preg_replace('/(^\s)|(\s$)/su', '', $title);

        return $title;
    }

    /**
     * Format Text Value
     *
     * @param string $text Input Text Value
     *
     * @return string Output Text Value
     */
    public function formatText(string $text = '') : string
    {
        $text = preg_replace('/\n+/su', "\n", $text);
        $text = preg_replace('/\n+/su', '<br>', $text);
        $text = preg_replace('/\s+/su', ' ', $text);
        $text = preg_replace('/(\<br\>\s)|(\s\<br\>)/su', '<br>', $text);
        $text = preg_replace('/\<br\>/su', "\n", $text);
        $text = preg_replace('/\n+/su', "\n", $text);
        $text = preg_replace('/(^\s)|(\s$)/su', '', $text);
        $text = str_replace('\'', '&#8217;', $text);

        return $text;
    }

    /**
     * Format Email Value
     *
     * @param string $email Input Email Value
     *
     * @return string Output Email Value
     */
    public function formatEmail(string $email = '') : string
    {
        $email = preg_replace('/\s+/su', '', $email);

        return $email;
    }

    /**
     * Format Slug Value
     *
     * @param string $slug Input Slug Value
     *
     * @return string Output Slug Value
     */
    public function formatSlug(string $slug = '') : string
    {
        $slug = preg_replace('/\s+/su', '', $slug);
        $slug = mb_convert_case($slug, MB_CASE_LOWER);

        return $slug;
    }

    /**
     * Format URL Value
     *
     * @param string $url Input URL Value
     *
     * @return string Output URL Value
     */
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
