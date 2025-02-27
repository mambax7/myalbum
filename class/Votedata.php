<?php declare(strict_types=1);

namespace XoopsModules\Myalbum;

require_once \dirname(__DIR__) . '/include/read_configs.php';

/**
 * Class for Blue Room Xcenter
 *
 * @author    Simon Roberts <simon@xoops.org>
 * @copyright copyright (c) 2009-2003 XOOPS.org
 */
final class Votedata extends \XoopsObject
{
    private $ratingid;
    private $lid;
    private $ratinguser;
    private $rating;
    private $ratinghostname;
    private $ratingtimestamp;

    /**
     * Votedata constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->initVar('ratingid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('lid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('ratinguser', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('rating', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('ratinghostname', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('ratingtimestamp', \XOBJ_DTYPE_INT, null, false);
    }
}
