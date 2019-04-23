<?php
/**
 * Generate And Display HTML From Template Page
 *
 * @param string $templatePage Template Page Name
 */
function renderPage(string $templatePage = '') : void
{
    foreach ($GLOBALS['templateParams'] as $param => $value) {
        $$param = $value;
    }

    $templatePageFile = $GLOBALS['templateDir'].$GLOBALS['templateScope'].
                        '/pages/'.$templatePage.'.tpl';

    if (!file_exists($templatePageFile)) {
        throw new Exception('Template Page "'.$templatePage.'" Missing');
    }

    include_once($templatePageFile);
}

/**
 * Generate And Display HTML From Template Part
 *
 * @param string $templatePart Part Template Part Name
 * @param int    $ttl          Time To Live Template Cache
 * @param array  $templateData Array Of Values For Using In Template Part
 *
 * @return bool Returning true After Displaying
 */
function renderPart(
    string $templatePart = '',
    int    $ttl          = 0,
    array  $templateData = []
) : bool
{
    if ($ttl>0) {
        if (is_file($GLOBALS['templateCacheDir'].'/'.$templatePart.'.dat')) {
            $partCacheData = file_get_contents(
                $GLOBALS['templateCacheDir'].'/'.$templatePart.'.dat'
            );
            $partCacheData = json_decode($partCacheData,true);
            if (
                isset($partCacheData['timestamp']) &&
                intval($partCacheData['timestamp']) > time() &&
                isset($partCacheData['data'])
            ) {
                echo $partCacheData['data'];

                return true;
            }
        } else {
            ob_start();
        }
    }

    foreach ($templateData as $templateDataItemIdx => $templateDataItem) {
        $GLOBALS['templateParams'][$templateDataItemIdx] = $templateDataItem;
    }

    foreach ($GLOBALS['templateParams'] as $param => $value) {
        $$param = $value;
    }

    $templatePartFile = $GLOBALS['templateDir'].$GLOBALS['templateScope'].
                        '/parts/'.$templatePart.'.tpl';

    if (!file_exists($templatePartFile)) {
        throw new Exception(
            'Template Part "'.$templatePart.'" Missing'
        );
    }

    include($templatePartFile);

    if ($ttl > 0) {
        $partContent = ob_get_clean();
        echo $partContent;
        $partCacheData = [
            'timestamp' => time() + $ttl,
            'data' => $partContent
        ];
        if (is_file($GLOBALS['templateCacheDir'].'/'.$templatePart.'.dat')) {
            unlink($GLOBALS['templateCacheDir'].'/'.$templatePart.'.dat');
        }
        file_put_contents(
            $GLOBALS['templateCacheDir'].'/'.$templatePart.'.dat',
            json_encode($partCacheData)
        );
    }

    return TRUE;
}

/**
 * Alias For renderPage Function
 *
 * @param string $templatePage Template Page Name
 */
function _page(string $templatePage = '') : void
{
    renderPage($templatePage);
}

/**
 * Alias For renderPart Function
 *
 * @param string $templatePart Part Template Part Name
 * @param int    $ttl          Time To Live Template Cache
 * @param array  $templateData Array Of Values For Using In Template Part
 *
 * @return bool Returning true After Displaying
 */
function _part(
    string $templatePart = '',
    array  $templateData = [],
    bool   $cache        = FALSE
) : void
{
    $ttl = $cache ? (int)$GLOBALS['templateTTL'] : 0;
    renderPart($templatePart, $ttl, $templateData);
}
?>
