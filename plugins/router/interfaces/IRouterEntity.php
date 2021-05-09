<?php
namespace Core\Plugins\Router\Interfaces;

interface IRouterEntity
{
    public function getArea(): string;

    public function getRoute(): string;

    public function getRoutePattern(): string;

    public function getParams(): ?array;

    public function getController(): string;

    public function getMethod(): string;

    public function getPage(): int;

    public function getLanguage(): ?string;

    public function setPage(?int $page = null): void;

    public function setLanguage(?string $language = null): void;

    public function setParams(?array $params = null): void;
}
