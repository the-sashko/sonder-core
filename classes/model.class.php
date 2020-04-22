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
    public $object = null;

    /**
     * @var string Model Values Object Class Name
     */
    public $voClassName = null;

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
     * @param string|null Model Object Class Name
     */
    public function setObject(?string $objectClassName = null): void
    {
        if (empty($objectClassName)) {
            throw new Exception('Model`s Object Class Name Is Not Set!');
        }

        if (null === $this->object) {
            $this->object = new $objectClassName();
            $this->object->initStore();
        }
    }

    /**
     * Set Model Value Object Class Name
     *
     * @param string|null Model Value Object Class Name
     */
    public function setValuesObjectClass(?string $voClassName = null): void
    {
        if (empty($voClassName)) {
            throw new Exception(
                'Model`s Values Object Class Name Is Not Set!'
            );
        }

        $this->voClassName = $voClassName;
    }

    /**
     * Set Config Main Data
     */
    public function setConfigData(): void
    {
        $configDataJSON   = file_get_contents(self::MAIN_CONFIG_PATH);
        $this->configData = json_decode($configDataJSON, true);
    }

    /**
     * Get String Value From Main Config Data
     *
     * @param string|null $valueName Value Name
     *
     * @return string|null Value Data
     */
    public function getConfigValue(?string $valueName = null): ?string
    {
        if (empty($valueName)) {
            return null;
        }

        if (!isset($this->configData[$valueName])) {
            return null;
        }

        return (string) $this->configData[$valueName];
    }

    /**
     * Get Array Value From Main Config Data
     *
     * @param string|null $valueName Value Name
     *
     * @return array|null Value Data
     */
    public function getConfigArrayValue(?string $valueName = null): ?array
    {
        if (empty($valueName)) {
            return null;
        }

        if (!isset($this->configData[$valueName])) {
            return null;
        }

        return (array) $this->configData[$valueName];
    }

    /**
     * Get Model Value Object Instance
     *
     * @param array|null $row List Of Values
     *
     * @return ValuesObject|null Values Object Instance
     */
    public function getVO(?array $row = null): ?ValuesObject
    {
        if (null === $row) {
            return null;
        }

        if (null === $this->voClassName) {
            throw new Exception('Value Object class not set');
        }

        return new $this->voClassName($row);
    }

    /**
     * Get List Of Model Value Object Instances
     *
     * @param array|null $rows List Of Values
     *
     * @return array|null List Of Model Value Object Instances
     */
    public function getVOArray(?array $rows = null): ?array
    {
        $voArray = [];

        if (null === $rows) {
            return null;
        }

        foreach ($rows as $row) {
            $vo = $this->getVO($row);

            if (!empty($vo)) {
                $voArray[] = $vo;
            }
        }

        return $voArray;
    }

    /**
     * Get Model Value Object Instance By ID Value
     *
     * @param int|null $id ID Value
     *
     * @return ValuesObject|null Values Object Instance
     */
    public function getByID(?int $id = null): ?ValuesObject
    {
        if (empty($id)) {
            return null;
        }

        $values = $this->object->getByID(
            $this->object->getDefaultTableName(),
            $id
        );

        if (empty($values)) {
            return null;
        }

        return $this->getVO($values);
    }

    /**
     * Get Model Value Object Instance By Slug Value
     *
     * @param string|null $slug Slug Value
     *
     * @return ValuesObject|null Values Object Instance
     */
    public function getBySlug(?string $slug = null): ?ValuesObject
    {
        if (empty($slug)) {
            return null;
        }

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
     * @return array|null List Of Model Value Object Instances
     */
    public function getByPage(int $page = 1): ?array
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
     * @param int|null $id ID Value
     *
     * @return bool Is Data From Data Base Successffuly Removed
     */
    public function removeByID(?int $id = null): bool
    {
        if (empty($id)) {
            throw new Exception('Model ID For Removing Is Not Set');
        }

        return $this->object->removeByID(
            $this->object->getDefaultTableName(),
            $id
        );
    }

    /**
     * Get Slug Value From String And Model Value ID
     *
     * @param string|null $input Input String
     * @param int|null    $id    ID Value
     *
     * @return string|null Output Slug Value
     */
    public function getSlug(?string $input = null, ?int $id = null): ?string
    {
        if (empty($input)) {
            return null;
        }

        $translit = $this->getPlugin('translit');
        $slug     = $translit->getSlug($input);

        return $this->_getUniqSlug($slug, $id);
    }

    /**
     * Get Unique Slug Value From Slug And Model Value ID
     *
     * @param string|null $input Input Slug Value
     * @param int|null    $id    ID Value
     *
     * @return string|null Output Slug Value
     */
    private function _getUniqSlug(
        ?string $slug = null,
        ?int    $id = null
    ): ?string
    {
        if (empty($slug)) {
            return null;
        }

        $condition = sprintf('"slug = \'%s\'', $slug);

        if (!empty($id)) {
            $condition = sprintf('%s AND "id" != %d', $id);
        }

        $row = $this->object->getOneByCondition(
            $this->object->getDefaultTableName(),
            [],
            $condition
        );

        if (!empty($row)) {
            return $slug;
        }

        $slugNumber = 1;

        if (preg_match('/^(.*?)\-([0-9]+)$/su', $slug)) {
            $slugNumber = (int) preg_replace(
                '/^(.*?)\-([0-9]+)/su',
                '$2',
                $slug
            );

            $slugNumber++;

            $slug = preg_replace('/^(.*?)\-([0-9]+)/su', '$1', $slug);
        }

        $slug = sprintf('%s-%d', $slug, $slugNumber);

        return $this->_getUniqSlug($slug, $id);
    }

    /**
     * Format Title Value
     *
     * @param string|null $title Input Title Value
     *
     * @return string|null Output Title Value
     */
    public function formatTitle(?string $title = null): ?string
    {
        if (empty($title)) {
            return null;
        }

        $title = preg_replace('/\s+/su', ' ', $title);
        $title = preg_replace('/(^\s)|(\s$)/su', '', $title);

        return $title;
    }

    /**
     * Format Text Value
     *
     * @param string|null $text Input Text Value
     *
     * @return string|null Output Text Value
     */
    public function formatText(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

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
     * @param string|null $email Input Email Value
     *
     * @return string|null Output Email Value
     */
    public function formatEmail(?string $email = null): ?string
    {
        if (empty($email)) {
            return null;
        }

        $email = preg_replace('/\s+/su', '', $email);

        return $email;
    }

    /**
     * Format Slug Value
     *
     * @param string|null $slug Input Slug Value
     *
     * @return string|null Output Slug Value
     */
    public function formatSlug(?string $slug = null): ?string
    {
        if (empty($slug)) {
            return null;
        }

        $slug = preg_replace('/\s+/su', '', $slug);
        $slug = mb_convert_case($slug, MB_CASE_LOWER);

        return $slug;
    }

    /**
     * Format URL Value
     *
     * @param string|null $url Input URL Value
     *
     * @return string|null Output URL Value
     */
    public function formatURL(?string $url = null): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = preg_replace('/\s+/su', '', $url);

        if (!preg_match('/^((http)|(https))\:\/\/(.*?)$/su', $url)) {
            $url = sprintf('http://%s', $url);
        }

        if (strlen($url) < 10) {
            $url = null;
        }

        return $url;
    }
}
