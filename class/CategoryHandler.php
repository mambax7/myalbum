<?php declare(strict_types=1);

namespace XoopsModules\Myalbum;

require \dirname(__DIR__) . '/include/read_configs.php';

/**
 * XOOPS policies handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 *
 * @author  Simon Roberts <simon@chronolabs.coop>
 */
class CategoryHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db = $db;
        parent::__construct($db, $GLOBALS['table_cat'], Category::class, 'cid', 'title');
    }

    /**
     * @param int $cid
     * @param int $depth
     *
     * @return int
     */
    public function prefixDepth($cid, int $depth = 0): int
    {
        $cat = $this->get($cid);
        ++$depth;
        if (0 != $cat->getVar('pid')) {
            $depth = $this->prefixDepth($cat->getVar('pid'), $depth);
        } else {
            $depth--;

            return $depth;
        }

        return $depth;
    }
}
