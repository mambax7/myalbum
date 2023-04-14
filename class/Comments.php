<?php declare(strict_types=1);

namespace XoopsModules\Myalbum;

require \dirname(__DIR__) . '/include/read_configs.php';

/**
 * Class for Blue Room Xcenter
 *
 * @author    Simon Roberts <simon@xoops.org>
 * @copyright copyright (c) 2009-2003 XOOPS.org
 */
final class Comments extends \XoopsObject
{
    private $com_id;
    private $com_pid;
    private $com_modid;
    private $com_icon;
    private $com_title;
    private $com_text;
    private $com_created;
    private $com_modified;
    private $com_uid;
    private $com_ip;
    private $com_sig;
    private $com_itemid;
    private $com_rootid;
    private $com_status;
    private $com_exparams;
    private $dohtml;
    private $dosmiley;
    private $doxcode;
    private $doimage;
    private $dobr;

    /**
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->initVar('com_id', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('com_pid', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_modid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('com_icon', \XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('com_title', \XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('com_text', \XOBJ_DTYPE_TXTAREA, null, true, null);
        $this->initVar('com_created', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_modified', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_uid', \XOBJ_DTYPE_INT, 0, true);
        $this->initVar('com_ip', \XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('com_sig', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_itemid', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_rootid', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_status', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_exparams', \XOBJ_DTYPE_OTHER, null, false, 255);
        $this->initVar('dohtml', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dosmiley', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doxcode', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doimage', \XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dobr', \XOBJ_DTYPE_INT, 0, false);
    }
}
