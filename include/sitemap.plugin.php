<?php declare(strict_types=1);

$moduleDirName = \basename(\dirname(__DIR__));
if (!preg_match('/^(\D+)(\d*)$/', $moduleDirName, $regs)) {
    echo('invalid dirname: ' . htmlspecialchars($moduleDirName, ENT_QUOTES | ENT_HTML5));
}
$mydirnumber = '' === $regs[2] ? '' : (int)$regs[2];

eval(
    'function b_sitemap_' . $moduleDirName . '(){
    $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();

    $block = sitemap_get_categories_map($xoopsDB->prefix(\'myalbum' . $mydirnumber . '_cat\'), \'cid\', \'pid\', \'title\', \'viewcat.php?cid=\', \'title\');

    return $block;
}

'
);
?>
