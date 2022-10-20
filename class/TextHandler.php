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
class TextHandler extends \XoopsPersistableObjectHandler
{
    public $db;
    /**
     * MyalbumTextHandler constructor.
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db = $db;
        parent::__construct($db, $GLOBALS['table_text'], Text::class, 'lid', 'description');
    }

    /**
     * @return string
     */
    public function getBytes(): string
    {
        $bytes  = '';
        $sql    = 'SELECT SUM(LENGTH(`description`)) AS `bytes` FROM ' . $GLOBALS['xoopsDB']->prefix($GLOBALS['table_text']);
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if ($GLOBALS['xoopsDB']->isResultSet($result)) {
            //            \trigger_error("Query Failed! SQL: $sql- Error: " . $GLOBALS['xoopsDB']->error(), E_USER_ERROR);
            [$bytes] = $GLOBALS['xoopsDB']->fetchRow($result);
        }

        return $bytes;
    }
}
