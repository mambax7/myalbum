<?php declare(strict_types=1);

namespace XoopsModules\Myalbum;

require_once \dirname(__DIR__) . '/include/read_configs.php';

/**
 * Class for Blue Room Xcenter
 *
 * @author    Simon Roberts <simon@xoops.org>
 * @copyright copyright (c) 2009-2003 XOOPS.org
 */
final class Photos extends \XoopsObject
{
    private $lid;
    private $cid;
    private $title;
    private $ext;
    private $res_x;
    private $res_y;
    private $submitter;
    private $status;
    private $date;
    private $hits;
    private $rating;
    private $votes;
    private $comments;
    private $tags;

    /**
     * MyalbumPhotos constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->initVar('lid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('cid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('title', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('ext', \XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('res_x', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('res_y', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('submitter', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('status', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('date', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('hits', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('rating', \XOBJ_DTYPE_DECIMAL, null, false);
        $this->initVar('votes', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('comments', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('tags', \XOBJ_DTYPE_TXTBOX, null, false, 255);
    }

    /**
     * @return string
     */
    public function getURL(): string
    {
        $helper = Helper::getInstance();
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = \xoops_getHandler('module');
        /** @var \XoopsConfigHandler $configHandler */
        $configHandler = \xoops_getHandler('config');
        if (!isset($GLOBALS['myalbumModule'])) {
            $GLOBALS['myalbumModule'] = $moduleHandler->getByDirname(
                Helper::getInstance()
                      ->getDirname()
            );
        }
        //        if (!isset($GLOBALS['myalbumModuleConfig'])) {
        //            $GLOBALS['myalbumModuleConfig'] = $configHandler->getConfigList($GLOBALS['myalbumModule']->getVar('mid'));
        //        }

        if ($helper->getConfig('htaccess')) {
            /** @var CategoryHandler $catHandler */
            $catHandler = $helper->getHandler('Category');
            $cat        = $catHandler->get($this->getVar('cid'));
            return XOOPS_URL . '/' . $helper->getConfig('baseurl') . '/' . \str_replace(
                    [
                        '_',
                        ' ',
                        ')',
                        '(',
                        '&',
                        '#',
                    ],
                    '-',
                    $cat->getVar('title')
                ) . '/' . \str_replace(
                              [
                                  '_',
                                  ' ',
                                  ')',
                                  '(',
                                  '&',
                                  '#',
                              ],
                              '-',
                              $this->getVar('title')
                          ) . '/' . $this->getVar('lid') . ',' . $this->getVar('cid') . $helper->getConfig('endofurl');
        }

        return $GLOBALS['mod_url'] . '/photo.php?lid=' . $this->getVar('lid') . '&cid=' . $this->getVar('cid');
    }

    /**
     * @return string
     */
    public function getEditURL(): string
    {
        $helper = Helper::getInstance();
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = \xoops_getHandler('module');
        /** @var \XoopsConfigHandler $configHandler */
        $configHandler = \xoops_getHandler('config');
        if (!isset($GLOBALS['myalbumModule'])) {
            $GLOBALS['myalbumModule'] = $moduleHandler->getByDirname(
                Helper::getInstance()
                      ->getDirname()
            );
        }
        //        if (!isset($GLOBALS['myalbumModuleConfig'])) {
        //            $GLOBALS['myalbumModuleConfig'] = $configHandler->getConfigList($GLOBALS['myalbumModule']->getVar('mid'));
        //        }

        if ($helper->getConfig('htaccess')) {
            /** @var CategoryHandler $catHandler */
            $catHandler = $helper->getHandler('Category');
            $cat        = $catHandler->get($this->getVar('cid'));
            return XOOPS_URL . '/' . $helper->getConfig('baseurl') . '/' . \str_replace(
                    [
                        '_',
                        ' ',
                        ')',
                        '(',
                        '&',
                        '#',
                    ],
                    '-',
                    $cat->getVar('title')
                ) . '/' . \str_replace(
                              [
                                  '_',
                                  ' ',
                                  ')',
                                  '(',
                                  '&',
                                  '#',
                              ],
                              '-',
                              $this->getVar('title')
                          ) . '/edit,' . $this->getVar('lid') . ',' . $this->getVar('cid') . $helper->getConfig('endofurl');
        }

        return $GLOBALS['mod_url'] . '/editphoto.php?lid=' . $this->getVar('lid') . '&cid=' . $this->getVar('cid');
    }

    /**
     * @return string
     */
    public function getRateURL(): string
    {
        $helper = Helper::getInstance();
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = \xoops_getHandler('module');
        /** @var \XoopsConfigHandler $configHandler */
        $configHandler = \xoops_getHandler('config');
        if (!isset($GLOBALS['myalbumModule'])) {
            $GLOBALS['myalbumModule'] = $moduleHandler->getByDirname(
                Helper::getInstance()
                      ->getDirname()
            );
        }
        //        if (!isset($GLOBALS['myalbumModuleConfig'])) {
        //            $GLOBALS['myalbumModuleConfig'] = $configHandler->getConfigList($GLOBALS['myalbumModule']->getVar('mid'));
        //        }

        if ($helper->getConfig('htaccess')) {
            /** @var CategoryHandler $catHandler */
            $catHandler = $helper->getHandler('Category');
            $cat        = $catHandler->get($this->getVar('cid'));
            return XOOPS_URL . '/' . $helper->getConfig('baseurl') . '/' . \str_replace(
                    [
                        '_',
                        ' ',
                        ')',
                        '(',
                        '&',
                        '#',
                    ],
                    '-',
                    $cat->getVar('title')
                ) . '/' . \str_replace(
                              [
                                  '_',
                                  ' ',
                                  ')',
                                  '(',
                                  '&',
                                  '#',
                              ],
                              '-',
                              $this->getVar('title')
                          ) . '/rate,' . $this->getVar('lid') . ',' . $this->getVar('cid') . $helper->getConfig('endofurl');
        }

        return $GLOBALS['mod_url'] . '/ratephoto.php?lid=' . $this->getVar('lid') . '&cid=' . $this->getVar('cid');
    }

    /**
     * @return string
     */
    public function getThumbsURL(): string
    {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = \xoops_getHandler('module');
        /** @var \XoopsConfigHandler $configHandler */
        $configHandler = \xoops_getHandler('config');
        if (!isset($GLOBALS['myalbumModule'])) {
            $GLOBALS['myalbumModule'] = $moduleHandler->getByDirname(
                Helper::getInstance()
                      ->getDirname()
            );
        }
        //        if (!isset($GLOBALS['myalbumModuleConfig'])) {
        //            $GLOBALS['myalbumModuleConfig'] = $configHandler->getConfigList($GLOBALS['myalbumModule']->getVar('mid'));
        //        }

        $url = $GLOBALS['thumbs_url'] . '/' . $this->getVar('lid') . '.' . $this->getVar('ext');

        return $url;
    }

    /**
     * @return string
     */
    public function getPhotoURL(): string
    {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = \xoops_getHandler('module');
        /** @var \XoopsConfigHandler $configHandler */
        $configHandler = \xoops_getHandler('config');
        if (!isset($GLOBALS['myalbumModule'])) {
            $GLOBALS['myalbumModule'] = $moduleHandler->getByDirname(
                Helper::getInstance()
                      ->getDirname()
            );
        }
        //        if (!isset($GLOBALS['myalbumModuleConfig'])) {
        //            $GLOBALS['myalbumModuleConfig'] = $configHandler->getConfigList($GLOBALS['myalbumModule']->getVar('mid'));
        //        }

        $url = $GLOBALS['photos_url'] . '/' . $this->getVar('lid') . '.' . $this->getVar('ext');

        return $url;
    }

    /**
     * @param bool $justVar
     *
     * @return array
     */
    public function toArray(bool $justVar = false): array
    {
        if ($justVar) {
            return parent::toArray();
        }
        $ret = [];
        $helper = Helper::getInstance();
        /** @var CategoryHandler $catHandler */
        $catHandler = $helper->getHandler('Category');
        /** @var TextHandler $textHandler */
        $textHandler = $helper->getHandler('Text');
        $userHandler = \xoops_getHandler('user');
        //mb        $statusHandler = xoops_getModuleHandler('status');
        $ret['photo'] = parent::toArray();

        $cat = $catHandler->get($this->getVar('cid'));

        if ($cat instanceof Category) {
            $ret['cat'] = $cat->toArray();
        }

        $text = $textHandler->get($this->getVar('lid'));
        if ($text instanceof Text) {
            $ret['text'] = $text->toArray();
        }

        $user = $userHandler->get($this->getVar('submitter'));
        //mb        $ret['status'] = $statusHandler->get($this->getVar('status'));
        $ret['status'] = $this->getVar('status'); //mb
        $ret['user']   = $user->toArray();

        return $ret;
    }

    /**
     * @param int $value
     *
     * @return mixed
     */
    public function increaseHits(int $value = 1)
    {
        $sql = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix($GLOBALS['table_photos']) . ' SET hits=hits+' . $value . " WHERE lid='" . $this->getVar('lid') . "' AND `status` > 0";
        return $GLOBALS['xoopsDB']->queryF($sql);
    }
}
