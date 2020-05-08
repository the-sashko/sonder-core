<?php
class StaticPageValuesObject
{
    private $_data = [];

    public function __construct(?array $staticPageData = null)
    {
        if (!empty($staticPageData)) {
            $this->_data = new ValuesObject($staticPageData);
        }
    }

    public function getTitle(): ?string
    {
        if (!$this->_data->has('title')) {
            return null;
        }

        return $this->_data->get('title');
    }

    public function getContent(): ?string
    {
        if (!$this->_data->has('content')) {
            return null;
        }

        return $this->_data->get('content');
    }
}
