<?php

namespace XoopsModules\Myalbum;



require \dirname(__DIR__) . '/include/read_configs.php';

/**
 * Class Myalbum1CatHandler
 */
class Myalbum1CatHandler extends CategoryHandler
{
    /**
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db);
    }
}
