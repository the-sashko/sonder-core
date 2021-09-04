<?php

/**
 * ValuesObject Class For Example Model
 */
class ExampleValuesObject extends ValuesObject
{
    /**
     * Get Example ID
     *
     * @return int Example ID
     *
     * @throws Exception
     */
    final public function getId(): int
    {
        return (int)$this->get('id');
    }

    /**
     * Get Foo Param Value
     *
     * @return string Foo Param Value
     *
     * @throws Exception
     */
    final public function getFoo(): string
    {
        return (string)$this->get('foo');
    }

    /**
     * Set Foo Param Value
     *
     * @param string|null $value
     *
     * @throws Exception
     */
    final public function setFoo(?string $value = null): void
    {
        $this->set('foo', $value);
    }
}
