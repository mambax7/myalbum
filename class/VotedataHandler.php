<?php

namespace XoopsModules\Myalbum;



require \dirname(__DIR__) . '/include/read_configs.php';

/**
 * XOOPS policies handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 *
 * @author  Simon Roberts <simon@chronolabs.coop>
 * @package kernel
 */
class VotedataHandler extends \XoopsPersistableObjectHandler
{
    /**
     * VotedataHandler constructor.
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db = $db;
        parent::__construct($db, $GLOBALS['table_votedata'], Votedata::class, 'ratingid', 'lid');
    }
}
