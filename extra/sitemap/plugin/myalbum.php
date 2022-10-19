<?php declare(strict_types=1);

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$mydirname = \basename(\dirname(__DIR__));
if (!preg_match('/^(\D+)(\d*)$/', $mydirname, $regs)) {
    echo('invalid dirname: ' . htmlspecialchars($mydirname, ENT_QUOTES | ENT_HTML5));
}
$mydirnumber = '' === $regs[2] ? '' : (int)$regs[2];

eval(
    '

function b_sitemap_' . $mydirname . '(){
    $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();

    $block = sitemap_get_categories_map($xoopsDB->prefix(\'myalbum' . $mydirnumber . '_cat\'), \'cid\', \'pid\', \'title\', \'viewcat.php?cid=\', \'title\');

    return $block;
}

'
);
