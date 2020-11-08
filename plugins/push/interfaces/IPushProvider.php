<?php
interface IPushProvider
{
    public function sendMessage(
        ?string $message = null,
        ?string $title   = null,
        ?string $image   = null,
        ?string $url     = null
    ): IPushResponse;

    public function getHtmlSnippet(): string;
}
