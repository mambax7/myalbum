<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link https://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author       XOOPS Development Team
 */

use Xmf\Request;
use XoopsModules\Myalbum\{
    Helper
};
/** @var Helper $helper */

require_once dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/include/read_configs.php';

$helper = Helper::getInstance();

$lid = Request::getInt('com_itemid', 0, 'GET');
if ($lid > 0) {
    $photosHandler  = $helper->getHandler('Photos');
    $photo          = $photosHandler->get($lid);
    $com_replytitle = $photo->getVar('title');

    if (!is_object($photo)) {
        exit('invalid lid');
    }

    require_once XOOPS_ROOT_PATH . '/include/comment_new.php';
}
