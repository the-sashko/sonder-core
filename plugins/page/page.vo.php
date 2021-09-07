<?php

class StaticPageValuesObject extends ValuesObject
{
    /**
     * @return string|null
     *
     * @throws Exception
     */
    public function getTitle(): ?string
    {
        if (!$this->has('title')) {
            return null;
        }

        return $this->get('title');
    }

    /**
     * @return string|null
     *
     * @throws Exception
     */
    public function getContent(): ?string
    {
        if (!$this->has('content')) {
            return null;
        }

        return $this->get('content');
    }

    /**
     * @param string|null $title
     *
     * @throws Exception
     */
    public function setTitle(?string $title = null): void
    {
        $this->set('title', $title);
    }

    /**
     * @param string|null $content
     *
     * @throws Exception
     */
    public function setContent(?string $content = null): void
    {
        $this->set('content', $content);
    }
}
