<?php declare(strict_types=1);

namespace XoopsModules\Myalbum;

require_once \dirname(__DIR__) . '/include/read_configs.php';

/**
 * Class Myalbum2VotedataHandler
 */
final class Myalbum2VotedataHandler extends VotedataHandler
{
    /**
     * Myalbum2VotedataHandler constructor.
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db);
    }
}
