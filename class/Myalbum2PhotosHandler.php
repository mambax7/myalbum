<?php

namespace XoopsModules\Myalbum;



require \dirname(__DIR__) . '/include/read_configs.php';

/**
 * Class Myalbum2PhotosHandler
 */
class Myalbum2PhotosHandler extends PhotosHandler
{
    /**
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db);
    }
}
