<?php declare(strict_types=1);

namespace XoopsModules\Myalbum;

require_once \dirname(__DIR__) . '/include/read_configs.php';

/**
 * XOOPS policies handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 *
 * @author  Simon Roberts <simon@chronolabs.coop>
 */
class PhotosHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var \XoopsDatabase|null|mixed
     */
    public $db;
    public $_table;
    public $_dirname;

    /**
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->_dirname = $GLOBALS['mydirname'];
        $this->_table   = $GLOBALS['table_photos'];
        parent::__construct($db, $this->_table, Photos::class, 'lid', 'title');
    }

    //    /**
    //     *
    //     * @param null
    //     * @param mixed $ids
    //     * @param mixed $status
    //     *
    //     * @return self
    //     */
    //    public static function getInstance()
    //    {
    //        static $instance = false;
    //        if (!$instance) {
    //            $instance = new self();
    //        }
    //    }

    /**
     * @param array $ids
     * @param int   $status
     *
     * @return bool
     */
    public function setStatus($ids, int $status = 1): bool
    {
        if (empty($ids)) {
            return false;
        }

        $where = \is_array($ids) ? "`lid` IN ('" . \implode("','", $ids) . "')" : "`lid` = '$ids'";

        $sql = 'UPDATE ' . $this->db->prefix($this->_table) . " SET `status`='$status' WHERE $where";
        $this->db->query($sql);

        if ($status === 1) {
            $helper = Helper::getInstance();
            /** @var CategoryHandler $catHandler */
            $catHandler = $helper->getHandler('Category');
            $cats       = $catHandler->getObjects(null, true);
            // Trigger Notification
            /** @var \XoopsNotificationHandler $notificationHandler */
            $notificationHandler = \xoops_getHandler('notification');
            $criteria            = new \Criteria('lid', "('" . \implode("','", $ids) . "')", 'IN');
            $photos              = $this->getObjects($criteria, true);
            foreach ($photos as $lid => $photo) {
                $notificationHandler->triggerEvent(
                    'global',
                    0,
                    'new_photo',
                    [
                        'PHOTO_TITLE' => $photo->getVar('title'),
                        'PHOTO_URI'   => $photo->getURL(),
                    ]
                );
                if ($photo->getVar('title') > 0 && \is_object($cats[$photo->getVar('cid')])) {
                    $notificationHandler->triggerEvent(
                        'category',
                        $photo->getVar('cid'),
                        'new_photo',
                        [
                            'PHOTO_TITLE'    => $photo->getVar('title'),
                            'CATEGORY_TITLE' => $cats[$photo->getVar('cid')]->getVar('title'),
                            'PHOTO_URI'      => $photo->getURL(),
                        ]
                    );
                }
            }
        }

        return true;
    }

    /**
     * @param $ids
     *
     * @return bool
     */
    public function deletePhotos($ids): bool
    {
        foreach ($ids as $lid) {
            @$this->delete($lid, true);
        }

        return true;
    }

    /**
     * @param \XoopsObject|int $photo
     * @param bool             $force
     * @return bool
     */
    public function delete(\XoopsObject $photo, $force = true): bool
    {
        if (\is_numeric($photo)) {
            $photo = $this->get($photo);
        }

        if (!$photo instanceof \XoopsModules\Myalbum\Photos) {
            return false;
        }

        \xoops_comment_delete($GLOBALS['myalbum_mid'], $photo->getVar('lid'));
        \xoops_notification_deletebyitem($GLOBALS['myalbum_mid'], 'photo', $photo->getVar('lid'));

        \unlink($GLOBALS['photos_dir'] . DS . $photo->getVar('lid') . '.' . $photo->getVar('ext'));
        \unlink($GLOBALS['photos_dir'] . DS . $photo->getVar('lid') . '.gif');
        \unlink($GLOBALS['thumbs_dir'] . DS . $photo->getVar('lid') . '.' . $photo->getVar('ext'));
        \unlink($GLOBALS['thumbs_dir'] . DS . $photo->getVar('lid') . '.gif');

        $helper = Helper::getInstance();

        /** @var VotedataHandler $votedataHandler */
        $votedataHandler = $helper->getHandler('Votedata');
        /** @var TextHandler $textHandler */
        $textHandler = $helper->getHandler('Text');
        /** @var CommentsHandler $commentsHandler */
        $commentsHandler = $helper->getHandler('Comments');
        $criteria        = new \Criteria('lid', $photo->getVar('lid'));
        $votedataHandler->deleteAll($criteria, $force);
        $textHandler->deleteAll($criteria, $force);

        return parent::delete($photo, $force);
    }

    /**
     * @param null $criteria
     *
     * @return int
     */
    public function getCountDeadPhotos($criteria = null): int
    {
        $objects = $this->getObjects($criteria, true);
        $i       = 0;
        foreach ($objects as $lid => $object) {
            if (!\is_readable($GLOBALS['photos_dir'] . DS . $lid . '.' . $object->getVar('ext'))) {
                ++$i;
            }
        }

        return $i;
    }

    /**
     * @param null $criteria
     *
     * @return int
     */
    public function getCountDeadThumbs($criteria = null): int
    {
        $objects = $this->getObjects($criteria, true);
        $i       = 0;
        foreach ($objects as $lid => $object) {
            if (!\is_readable($GLOBALS['thumbs_dir'] . DS . $lid . '.' . $object->getVar('ext'))) {
                ++$i;
            }
        }

        return $i;
    }

    /**
     * @param null $criteria
     *
     * @return array
     */
    public function getDeadPhotos($criteria = null): array
    {
        $objects = $this->getObjects($criteria, true);
        foreach ($objects as $lid => $object) {
            if (\is_readable($GLOBALS['photos_dir'] . DS . $lid . '.' . $object->getVar('ext'))) {
                unset($objects[$lid]);
            }
        }

        return $objects;
    }

    /**
     * @param null $criteria
     *
     * @return array
     */
    public function getDeadThumbs($criteria = null): array
    {
        $objects = $this->getObjects($criteria, true);
        foreach ($objects as $lid => $object) {
            if (\is_readable($GLOBALS['thumbs_dir'] . DS . $lid . '.' . $object->getVar('ext'))) {
                unset($objects[$lid]);
            }
        }

        return $objects;
    }
}
