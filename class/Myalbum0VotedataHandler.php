<?php

namespace XoopsModules\Myalbum;



require \dirname(__DIR__) . '/include/read_configs.php';



/**
 * Class Myalbum0VotedataHandler
 */
class Myalbum0VotedataHandler extends VotedataHandler
{
    /**
     * Myalbum0VotedataHandler constructor.
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db);
    }
}
