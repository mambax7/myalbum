<?php

namespace XoopsModules\Myalbum;

require \dirname(__DIR__) . '/include/read_configs.php';

 /**
 * Class Myalbum1CommentsHandler
 */
class Myalbum1CommentsHandler extends CommentsHandler
{
    /**
     * Myalbum1CommentsHandler constructor.
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db);
    }
}
