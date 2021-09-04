<?php

class ExampleHook extends HookCore
{
    final public function setExampleData(): void
    {
        $this->setEntityParam('exampleHookValue', 'Example Data');
    }
}
