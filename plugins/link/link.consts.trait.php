<?php
trait LinkPluginConsts {
    const META_TAG_OG_TITLE_REGEX = '/^(.*?)\<meta([\s]+)'.
                                    'property=(\"|\')og\:title(\"|\')([\s]+)'.
                                    'content=(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_TITLE_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                        'content=(\"|\')(.*?)\"([\s]+)'.
                                        'property=(\"|\')'.
                                        'og\:title(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_TITLE_REGEX = '/^(.*?)\<meta([\s]+)'.
                                         'name=(\"|\')twitter\:title'.
                                         '(\"|\')([\s]+)content='.
                                         '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_TITLE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                             '(\"|\')(.*?)\"([\s]+)'.
                                             'name=(\"|\')twitter\:title'.
                                             '(\"|\')(.*?)$/su';

    const META_TITLE_REGEX = '/^(.*?)\<title([\s]+|)\>'.
                             '(.*?)\<\/title\>(.*?)$/su';

    const H1_TITLE_REGEX = '/^(.*?)\<h1(.*?)\>(.*?)\<\/h1\>(.*?)$/su';

    const H1_TITLE_REGEX_ALT = '/^(.*?)\<h1(.*?)\>(.*?)\<\/h1\>(.*?)$/su';

    const MAIN_TITLE_REGEX = '/^(.*?)\<main(.*?)\>(.*?)\<\/main\>(.*?)$/su';

    const BODY_TITLE_REGEX = '/^(.*?)\<body(.*?)\>(.*?)\<\/body\>(.*?)$/su';

    const META_TAG_OG_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)property='.
                                          '(\"|\')og\:description'.
                                          '(\"|\')([\s]+)content='.
                                          '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                              '(\"|\')(.*?)\"([\s]+)property='.
                                              '(\"|\')og\:description'.
                                              '(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)name='.
                                               '(\"|\')twitter\:description'.
                                               '(\"|\')([\s]+)content='.
                                               '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                                   'content=(\"|\')'.
                                                   '(.*?)\"([\s]+)name='.
                                                   '(\"|\')twitter\:'.
                                                   'description(\"|\')'.
                                                   '(.*?)$/su';

    const META_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)'.
                                   'name=(\"|\')description(\"|\''.
                                   ')([\s]+)content=(\"|\')(.*?)'.
                                   '(\"|\')(.*?)$/su';

    const META_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                       'content=(\"|\')(.*?)\"([\s]+)'.
                                       'name=(\"|\')description(\"|\')'.
                                       '(.*?)$/su';

    const ARTICLE_DESCRIPTION_REGEX = '/^(.*?)\<article(.*?)\>(.*?)'.
                                      '\<\/article\>(.*?)$/su';

    const MAIN_DESCRIPTION_REGEX = '/^(.*?)\<main(.*?)\>(.*?)\<\/main\>'.
                                   '(.*?)$/su';

    const P_DESCRIPTION_REGEX = '/^(.*?)\<p(.*?)\>(.*?)\<\/p\>(.*?)$/su';

    const BODY_DESCRIPTION_REGEX = '/^(.*?)\<body(.*?)\>(.*?)\<\/body\>'.
                                   '(.*?)$/su';

    const META_TAG_OG_IMAGE_REGEX = '/^(.*?)\<meta([\s]+)property=(\"|\')'.
                                    'og\:image(\"|\')([\s]+)'.
                                    'content=(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_IMAGE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                        '(\"|\')(.*?)\"([\s]+)property='.
                                        '(\"|\')og\:image(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_IMAGE_REGEX = '/^(.*?)\<meta([\s]+)name=(\"|\')'.
                                         'twitter\:image(\"|\')([\s]+)'.
                                         'content=(\"|\')(.*?)(\"|\')'.
                                         '(.*?)$/su';

    const META_TAG_TWITTER_IMAGE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                             '(\"|\')(.*?)\"([\s]+)name='.
                                             '(\"|\')twitter\:image(\"|\')'.
                                             '(.*?)$/su';

    const LINK_IMAGE_REGEX = '/^(.*?)\<link([\s]+)rel=(\"|\')'.
                             'image_src(\"|\')([\s]+)href=(\"|\')'.
                             '(.*?)(\"|\')(.*?)$/su';

    const LINK_IMAGE_REGEX_ALT = '/^(.*?)\<link([\s]+)href=(\"|\')'.
                                 '(.*?)\"([\s]+)rel=(\"|\')'.
                                 'image_src(\"|\')(.*?)$/su';

    const IMG_IMAGE_REGEX_ALT = '/^(.*?)\<img(.*?)src=(\"|\')(.*?)(\"|\')'.
                                '(.*?)\>(.*?)$/su';
}
?>