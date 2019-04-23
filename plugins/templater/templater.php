<?php
/**
 * Plugin For Rendering Templates
 */
class TemplaterPlugin
{
    /**
     * @var string Templates Directory Path
     */
    const TEMPLATE_DIR = __DIR__.'/../../../res/tpl/';

    /**
     * @var string Templates Scope
     */
    public $scope = 'site';

    /**
     * Generate And Display HTML Page From Template File
     *
     * @param string $template   Template File Name
     * @param array  $dataParams Array Of Values For Using In Template Page
     * @param int    $ttl        Time To Live Template Cache
     */
    public function render(
        string $template   = 'main',
        array  $dataParams = [],
        int    $ttl        = 0
    ) : void
    {
        $GLOBALS['templateDir'] = static::TEMPLATE_DIR;

        if (!isset($GLOBALS['templateParams'])) {
            $GLOBALS['templateParams'] = [];
        }

        $GLOBALS['templateParams'] = $dataParams;
        $GLOBALS['templateScope']  = $this->scope;
        $GLOBALS['templateTTL']    = $ttl;

        $template = strlen($template) > 0 ? $template : 'main';

        if ($ttl>0) {
            $currSlug    = $_SERVER['REQUEST_URI'];
            $currSlug    = str_replace('/', '_', $currSlug);
            $currSlug    = preg_replace('/(^_)|(_$)/su', '', $currSlug);
            $tplCacheDir = static::TEMPLATE_DIR.$currSlug;

            if (!is_dir($tplCacheDir)) {
                mkdir($tplCacheDir);
                chmod($tplCacheDir, 0775);
            }

            $tplCacheDir = $tplCacheDir.'/'.$this->scope;
            if (!is_dir($tplCacheDir)) {
                mkdir($tplCacheDir);
                chmod($tplCacheDir, 0775);
            }

            $tplCacheDir = "{$tplCacheDir}/{$template}";
            if (!is_dir($tplCacheDir)) {
                mkdir($tplCacheDir);
                chmod($tplCacheDir, 0775);
            }

            $GLOBALS['templateCacheDir'] = $tplCacheDir;
        }

        foreach ($GLOBALS['templateParams'] as $param => $value) {
            $$param = $value;
        }

        if (
            !file_exists(static::TEMPLATE_DIR.$this->scope.'/index.tpl')
        ) {
            throw new Exception('Template "'.$this->scope.'" Missing');
        }

        include_once(static::TEMPLATE_DIR.$this->scope.'/index.tpl');

        exit(0);
    }
}
?>
