<?php
/**
 * Class For Working With Route Data
 */
class RouteObject extends ValuesObject
{
    /**
     * Get Route Area
     *
     * @return string|null Route Area
     */
    public function getArea(): ?string
    {
        if (!$this->has('area')) {
            return null;
        }

        return $this->get('area');
    }

    /**
     * Get Route Path
     *
     * @return string|null Route Path
     */
    public function getRoute(): ?string
    {
        if (!$this->has('route')) {
            return null;
        }

        return $this->get('route');
    }

    /**
     * Get Url Params
     *
     * @return string|null Url Params
     */
    public function getUrlParams(): ?string
    {
        if (!$this->has('params')) {
            return null;
        }

        return $this->get('params');
    }

    /**
     * Get Controller
     *
     * @return string|null Controller
     */
    public function getController(): ?string
    {
        if (!$this->has('controller')) {
            return null;
        }

        return $this->get('controller');
    }

    /**
     * Get Method
     *
     * @return string|null Method
     */
    public function getMethod(): ?string
    {
        if (!$this->has('method')) {
            return null;
        }

        return $this->get('method');
    }

    /**
     * Get Hash
     *
     * @return string
     */
    public function getHash(): string
    {
        $area  = (string) $this->getArea();
        $route = (string) $this->getRoute();

        $hash = sprintf('%s %s', $area, $route);

        return sprintf('%s%s', hash('sha256', $hash), hash('md5', $hash));
    }
}
