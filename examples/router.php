<?php
trait Router
{
    public function routeRedirect(string $uri = '') : void
    {
        // Redirect Rules
    }

    public function routeRewrite(string $uri = '') : string
    {
        // Rewrite Rules
        return $uri;
    }
}
?>