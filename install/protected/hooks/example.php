<?php
class ExampleHook extends HookCore
{
    public function setExampleData(): void
    {
        $this->setEntityParam('exampleHookValue', 'Example Data');
    }
}