<?php

namespace Sonder\Interfaces;

use Attribute;

#[IController]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICronController extends IController
{
    /**
     * @return IResponseObject
     */
    public function displayRun(): IResponseObject;

    /**
     * @return void
     */
    public function jobRouter(): void;

    /**
     * @return void
     */
    public function jobTranslations(): void;
}
