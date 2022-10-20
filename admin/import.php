<?php declare(strict_types=1);
// ------------------------------------------------------------------------- //
//                      myAlbum-P - XOOPS photo album                        //
//                        <https://www.peak.ne.jp>                           //
// ------------------------------------------------------------------------- //

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Myalbum\{
    Forms,
    Utility
};

require_once __DIR__ . '/admin_header.php';
include_once XOOPS_ROOT_PATH . '/modules/system/constants.php';

// From myalbum*
if (!empty($_POST['myalbum_import']) && !empty($_POST['cid'])) {
    // anti-CSRF
    $xsecurity = new XoopsSecurity();
    if (!$xsecurity->checkReferer()) {
        exit('XOOPS_URL is not included in your REFERER');
    }

    // get src module
    $src_cid     = Request::getInt('cid', 0, 'POST');
    $src_dirname = Request::getString('src_dirname', '', 'POST');
    if ($moduleDirName === $src_dirname) {
        exit('source dirname is same as dest dirname: ' . htmlspecialchars($src_dirname, ENT_QUOTES | ENT_HTML5));
    }
    if (!preg_match('/^myalbum(\d*)$/', $src_dirname, $regs)) {
        exit('invalid dirname of myalbum: ' . htmlspecialchars($src_dirname, ENT_QUOTES | ENT_HTML5));
    }
    /** @var \XoopsModuleHandler $moduleHandler */
    $moduleHandler = xoops_getHandler('module');
    $module        = $moduleHandler->getByDirname($src_dirname);
    if (!is_object($module)) {
        exit('invalid module dirname:' . htmlspecialchars($src_dirname, ENT_QUOTES | ENT_HTML5));
    }
    $src_mid = $module->getVar('mid');

    // authority check
    if (!$GLOBALS['xoopsUser']->isAdmin($src_mid)) {
        exit;
    }

    // read configs from xoops_config directly
    $sql = 'SELECT conf_name,conf_value FROM  ' . $GLOBALS['xoopsDB']->prefix('config') . " WHERE conf_modid='$src_mid'";
    $rs  = $GLOBALS['xoopsDB']->query($sql);
    while ([$key, $val] = $GLOBALS['xoopsDB']->fetchRow($rs)) {
        $src_configs[$key] = $val;
    }
    $src_photos_dir = XOOPS_ROOT_PATH . $src_configs['myalbum_photospath'];
    $src_thumbs_dir = XOOPS_ROOT_PATH . $src_configs['myalbum_thumbspath'];
    // src table names
    $src_table_photos   = $GLOBALS['xoopsDB']->prefix("{$src_dirname}_photos");
    $src_table_cat      = $GLOBALS['xoopsDB']->prefix("{$src_dirname}_cat");
    $src_table_text     = $GLOBALS['xoopsDB']->prefix("{$src_dirname}_text");
    $src_table_votedata = $GLOBALS['xoopsDB']->prefix("{$src_dirname}_votedata");

    $move_mode = Request::hasVar('copyormove', 'POST') && 'move' === $_POST['copyormove'];

    // create category
    $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix($table_cat) . "(pid, title, imgurl) SELECT '0',title,imgurl FROM $src_table_cat WHERE cid='$src_cid'";
    $GLOBALS['xoopsDB']->query($sql)
    || exit('DB error: INSERT cat table');
    $cid = $GLOBALS['xoopsDB']->getInsertId();

    // INSERT loop
    $sql          = "SELECT lid,ext FROM $src_table_photos WHERE cid='$src_cid'";
    $rs           = $GLOBALS['xoopsDB']->query($sql);
    $import_count = 0;
    while ([$src_lid, $ext] = $GLOBALS['xoopsDB']->fetchRow($rs)) {
        // photos table
        $set_comments = $move_mode ? 'comments' : "'0'";
        $sql          = 'INSERT INTO '
                        . $GLOBALS['xoopsDB']->prefix($table_photos)
                        . "(cid,title,ext,res_x,res_y,submitter,`status`,date,hits,rating,votes,comments) SELECT '$cid',title,ext,res_x,res_y,submitter,`status`,date,hits,rating,votes,$set_comments FROM $src_table_photos WHERE lid='$src_lid'";
        $result       = $GLOBALS['xoopsDB']->query($sql);
        if (!$GLOBALS['xoopsDB']->isResultSet($result)) {
            throw new \RuntimeException("DB error: INSERT photo table! SQL: $sql- Error: " . $GLOBALS['xoopsDB']->error());
        }
        $lid = $GLOBALS['xoopsDB']->getInsertId();

        // text table
        $sql = 'INSERT INTO  ' . $GLOBALS['xoopsDB']->prefix($table_text) . " (lid,description) SELECT '$lid',description FROM $src_table_text WHERE lid='$src_lid'";
        $GLOBALS['xoopsDB']->query($sql);

        // votedata table
        $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix($table_votedata) . " (lid,ratinguser,rating,ratinghostname,ratingtimestamp) SELECT '$lid',ratinguser,rating,ratinghostname,ratingtimestamp FROM $src_table_votedata WHERE lid='$src_lid'";
        $GLOBALS['xoopsDB']->query($sql);

        @copy("$src_photos_dir/{$src_lid}.{$ext}", "$photos_dir/{$lid}.{$ext}");
        if (\in_array(\mb_strtolower($ext), $myalbum_normal_exts, true)) {
            @copy("$src_thumbs_dir/{$src_lid}.{$ext}", "$thumbs_dir/{$lid}.{$ext}");
        } else {
            @copy("$src_photos_dir/{$src_lid}.gif", "$photos_dir/{$lid}.gif");
            @copy("$src_thumbs_dir/{$src_lid}.gif", "$thumbs_dir/{$lid}.gif");
        }

        // exec only moving mode
        if ($move_mode) {
            // moving comments
            $sql = 'UPDATE  ' . $GLOBALS['xoopsDB']->prefix('xoopscomments') . " SET com_modid='$myalbum_mid',com_itemid='$lid' WHERE com_modid='$src_mid' AND com_itemid='$src_lid'";
            $GLOBALS['xoopsDB']->query($sql);
            // delete source photos
            $photos_dir = $src_photos_dir;
            $thumbs_dir = $src_thumbs_dir;
            $myalbum_mid = $src_mid;
            $table_photos = $src_table_photos;
            $table_text = $src_table_text;
            $table_votedata = $src_table_votedata;
            $saved_photos_dir = $photos_dir;
            $saved_thumbs_dir = $thumbs_dir;
            $saved_myalbum_mid = $myalbum_mid;
            $saved_table_photos = $GLOBALS['xoopsDB']->prefix($table_photos);
            $saved_table_text = $GLOBALS['xoopsDB']->prefix($table_text);
            $saved_table_votedata = $GLOBALS['xoopsDB']->prefix($table_votedata);
            Utility::deletePhotos("lid='$src_lid'");
            $photos_dir = $saved_photos_dir;
            $thumbs_dir = $saved_thumbs_dir;
            $myalbum_mid = $saved_myalbum_mid;
            $table_photos = $saved_table_photos;
            $table_text = $saved_table_text;
            $table_votedata = $saved_table_votedata;
        }

        ++$import_count;
    }

    redirect_header('import.php', 2, sprintf(_AM_FMT_IMPORTSUCCESS, $import_count));
} // From imagemanager
elseif (!empty($_POST['imagemanager_import']) && !empty($_POST['imgcat_id'])) {
    // authority check
    /** @var \XoopsGroupPermHandler $grouppermHandler */
    $grouppermHandler = xoops_getHandler('groupperm');
    if (!$grouppermHandler->checkRight('system_admin', XOOPS_SYSTEM_IMAGE, $GLOBALS['xoopsUser']->getGroups())) {
        exit;
    }

    // anti-CSRF
    $xsecurity = new XoopsSecurity();
    if (!$xsecurity->checkReferer()) {
        exit('XOOPS_URL is not included in your REFERER');
    }

    // get src information
    $src_cid          = Request::getInt('imgcat_id', 0, 'POST');
    $src_table_photos = $GLOBALS['xoopsDB']->prefix('image');
    $src_table_cat    = $GLOBALS['xoopsDB']->prefix('imagecategory');

    // create category
    $sql = "SELECT imgcat_name,imgcat_storetype FROM $src_table_cat WHERE imgcat_id='$src_cid'";
    $crs = $GLOBALS['xoopsDB']->query($sql);
    [$imgcat_name, $imgcat_storetype] = $GLOBALS['xoopsDB']->fetchRow($crs);

    $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix($table_cat) . " SET pid=0,title='" . addslashes($imgcat_name) . "'";
    $GLOBALS['xoopsDB']->query($sql)
    || exit('DB error: INSERT cat table');
    $cid = $GLOBALS['xoopsDB']->getInsertId();

    // INSERT loop
    $sql          = "SELECT image_id,image_name,image_nicename,image_created,image_display FROM $src_table_photos WHERE imgcat_id='$src_cid'";
    $rs           = $GLOBALS['xoopsDB']->query($sql);
    $import_count = 0;
    while ([$image_id, $image_name, $image_nicename, $image_created, $image_display] = $GLOBALS['xoopsDB']->fetchRow($rs)) {
        $src_file = XOOPS_UPLOAD_PATH . "/$image_name";
        $ext      = mb_substr(\mb_strrchr($image_name, '.'), 1);

        // photos table
        $sql    = 'INSERT INTO  ' . $GLOBALS['xoopsDB']->prefix($table_photos) . " SET cid='$cid',title='" . addslashes($image_nicename) . "',ext='$ext',submitter='$my_uid',`status`='$image_display',date='$image_created'";
        $result = $GLOBALS['xoopsDB']->query($sql);
        if (!$result) {
            throw new \RuntimeException("DB error: INSERT photo table! SQL: $sql- Error: " . $GLOBALS['xoopsDB']->error());
        }
        $lid = $GLOBALS['xoopsDB']->getInsertId();

        // text table
        $sql = 'INSERT INTO  ' . $GLOBALS['xoopsDB']->prefix($table_text) . " SET lid='$lid',description=''";
        $GLOBALS['xoopsDB']->query($sql);

        $dst_file = "$photos_dir/{$lid}.{$ext}";
        if ('db' === $imgcat_storetype) {
            $fp = fopen($dst_file, 'wb');
            if (false === $fp) {
                continue;
            }
            $sql = 'SELECT image_body FROM  ' . $GLOBALS['xoopsDB']->prefix('imagebody') . " WHERE image_id='$image_id'";
            $brs = $GLOBALS['xoopsDB']->query($sql);
            [$body] = $GLOBALS['xoopsDB']->fetchRow($brs);
            fwrite($fp, $body);
            fclose($fp);
            Utility::createThumb($dst_file, $lid, $ext);
        } else {
            @copy($src_file, $dst_file);
            Utility::createThumb($src_file, $lid, $ext);
        }

        [$width, $height, $type] = getimagesize($dst_file);
        $sql = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix($table_photos) . " SET res_x='$width',res_y='$height' WHERE lid='$lid'";
        $GLOBALS['xoopsDB']->query($sql);

        ++$import_count;
    }

    redirect_header('import.php', 2, sprintf(_AM_FMT_IMPORTSUCCESS, $import_count));
}

require_once \dirname(__DIR__) . '/include/myalbum.forms.php';
xoops_cp_header();
$adminObject = Admin::getInstance();
$adminObject->displayNavigation(basename(__FILE__));
//myalbum_adminMenu(basename(__FILE__), 6);
$GLOBALS['xoopsTpl']->assign('admin_title', sprintf(_AM_H3_FMT_IMPORTTO, $xoopsModule->name()));
$GLOBALS['xoopsTpl']->assign('mydirname', $GLOBALS['mydirname']);
$GLOBALS['xoopsTpl']->assign('photos_url', $GLOBALS['photos_url']);
$GLOBALS['xoopsTpl']->assign('thumbs_url', $GLOBALS['thumbs_url']);
$GLOBALS['xoopsTpl']->assign('forma', Forms::getAdminFormImportMyalbum());
$GLOBALS['xoopsTpl']->assign('formb', Forms::getAdminFormImportImageManager());

$GLOBALS['xoopsTpl']->display('db:' . $GLOBALS['mydirname'] . '_cpanel_import.tpl');

//  myalbum_footer_adminMenu();
require_once __DIR__ . '/admin_footer.php';
