<?php declare(strict_types=1);

use XoopsModules\Myalbum\{
    Helper,
    Utility
};

/** @var Helper $helper */

$helper        = Helper::getInstance();
$moduleDirName = $helper->getDirname();

$GLOBALS['mydirname'] = \basename(\dirname(__DIR__));
if (preg_match('/^myalbum(\d*)$/', $GLOBALS['mydirname'], $regs)) {
    $GLOBALS['myalbum_number'] = $regs[1];
} else {
    exit('invalid dirname of myalbum: ' . htmlspecialchars($GLOBALS['mydirname'], ENT_QUOTES | ENT_HTML5));
}

global $xoopsConfig, $xoopsDB, $xoopsUser;
/** @var \XoopsModuleHandler $moduleHandler */
$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->getByDirname($GLOBALS['mydirname']);

// module information
$GLOBALS['mod_url']       = XOOPS_URL . "/modules/{$GLOBALS['mydirname']}";
$GLOBALS['mod_path']      = XOOPS_ROOT_PATH . "/modules/{$GLOBALS['mydirname']}";
$GLOBALS['mod_copyright'] = "<a href='https://xoops.org/'><strong>myAlbum-P " . $module->getInfo('version') . ' by XOOPS</strong></a>';

// global language file
xoops_loadLanguage('myalbum_constants', $GLOBALS['mydirname']);

// read from xoops_config
// get my mid
/** @var \XoopsModuleHandler $moduleHandler */
$moduleHandler            = xoops_getHandler('module');
$GLOBALS['myalbumModule'] = $moduleHandler->getByDirname($GLOBALS['mydirname']);
if (is_object($GLOBALS['myalbumModule'])) {
    $GLOBALS['myalbum_mid'] = $GLOBALS[$GLOBALS['mydirname'] . 'Module']->getVar('mid');
    /** @var \XoopsConfigHandler $configHandler */
    $configHandler = xoops_getHandler('config');
    // read configs from xoops_config directly
    $GLOBALS['myalbumModuleConfig'] = $configHandler->getConfigList($GLOBALS['myalbum_mid']);
    extract($GLOBALS['myalbumModuleConfig']);
}

// User Information
if (empty($GLOBALS['xoopsUser'])) {
    $my_uid  = 0;
    $isadmin = false;
} else {
    $my_uid  = $GLOBALS['xoopsUser']->uid();
    $isadmin = $GLOBALS['xoopsUser']->isAdmin($GLOBALS['myalbum_mid']);
}

// Value Check
$GLOBALS['myalbum_addposts'] = isset($GLOBALS['myalbum_addposts']) ? (int)$GLOBALS['myalbum_addposts'] : 0;
if ($GLOBALS['myalbum_addposts'] < 0) {
    $GLOBALS['myalbum_addposts'] = 0;
}

// Path to Main Photo & Thumbnail ;
if (isset($GLOBALS['myalbum_photospath'])) {
    if (0x2f != ord($GLOBALS['myalbum_photospath'])) {
        $GLOBALS['myalbum_photospath'] = DS . $GLOBALS['myalbum_photospath'];
    }
} else {
    $GLOBALS['myalbum_photospath'] = "/uploads/{$moduleDirName}/photos/";
}
if (isset($GLOBALS['myalbum_thumbspath'])) {
    if (0x2f != ord($GLOBALS['myalbum_thumbspath'])) {
        $GLOBALS['myalbum_thumbspath'] = DS . $GLOBALS['myalbum_thumbspath'];
    }
} else {
    $GLOBALS['myalbum_thumbspath'] = "/uploads/{$moduleDirName}/thumbs/";
}
$photos_dir = XOOPS_ROOT_PATH . $GLOBALS['myalbum_photospath'];
$photos_url = XOOPS_URL . $GLOBALS['myalbum_photospath'];
if (isset($GLOBALS['myalbum_makethumb'])) {
    $thumbs_dir = XOOPS_ROOT_PATH . $GLOBALS['myalbum_thumbspath'];
    $thumbs_url = XOOPS_URL . $GLOBALS['myalbum_thumbspath'];
} else {
    $thumbs_dir = $photos_dir;
    $thumbs_url = $photos_url;
}

// DB table name Original
//$table_photos   = $xoopsDB->prefix("myalbum{$mydirnumber}_photos");
//$table_cat      = $xoopsDB->prefix("myalbum{$mydirnumber}_cat");
//$table_text     = $xoopsDB->prefix("myalbum{$mydirnumber}_text");
//$table_votedata = $xoopsDB->prefix("myalbum{$mydirnumber}_votedata");
//$table_comments = $xoopsDB->prefix('xoopscomments');

// DB table name
$GLOBALS['table_photos']   = "{$GLOBALS['mydirname']}_photos";
$GLOBALS['table_cat']      = "{$GLOBALS['mydirname']}_cat";
$GLOBALS['table_text']     = "{$GLOBALS['mydirname']}_text";
$GLOBALS['table_votedata'] = "{$GLOBALS['mydirname']}_votedata";
$GLOBALS['table_comments'] = 'xoopscomments';

// Pipe environment check
if (isset($GLOBALS['myalbum_imagingpipe'])) {
    $GLOBALS['myalbum_canrotate'] = $GLOBALS['myalbum_imagingpipe'] || function_exists('imagerotate');
}
if (isset($GLOBALS['myalbum_imagingpipe'])) {
    $GLOBALS['myalbum_canresize'] = $GLOBALS['myalbum_imagingpipe'] || $GLOBALS['myalbum_forcegd2'];
}
// Normal Extensions of Image
$GLOBALS['myalbum_normal_exts'] = ['jpg', 'jpeg', 'gif', 'png'];

// Allowed extensions & MIME types
if (empty($GLOBALS['myalbum_allowedexts'])) {
    $GLOBALS['array_allowed_exts'] = $GLOBALS['myalbum_normal_exts'];
} else {
    $GLOBALS['array_allowed_exts'] = explode('|', $GLOBALS['myalbum_allowedexts']);
}
if (empty($GLOBALS['myalbum_allowedmime'])) {
    $GLOBALS['array_allowed_mimetypes'] = [];
} else {
    $GLOBALS['array_allowed_mimetypes'] = explode('|', $GLOBALS['myalbum_allowedmime']);
}
