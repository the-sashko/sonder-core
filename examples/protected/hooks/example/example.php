<?php
class ExampleHook extends HookCore
{
    public function getExampleData() : void
    {
        $this->setEntityParam(
            'ExampleHookValue',
            'Example Data'
        );
    }
}