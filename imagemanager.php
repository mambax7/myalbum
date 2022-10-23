<?php declare(strict_types=1);

use Xmf\Request;

if (!defined('XOOPS_ROOT_PATH')) {
    require_once __DIR__ . '/header.php';
} else {
    // when this script is included by core's imagemanager.php
    $moduleDirName = basename(__DIR__);
    require XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/read_configs.php";
}

require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/get_perms.php";
require_once XOOPS_ROOT_PATH . '/class/template.php';

// Get variables
if (empty($_GET['target'])) {
    exit;
}
$num = Request::getInt('num', 10, 'GET');
$cid = Request::getInt('cid', 0, 'GET');

$xoopsTpl = new \XoopsTpl();
$xoopsTpl->assign('lang_imgmanager', _IMGMANAGER);
$xoopsTpl->assign('sitename', $xoopsConfig['sitename']);
$target = htmlspecialchars($_GET['target'], ENT_QUOTES);
$xoopsTpl->assign('target', $target);
$xoopsTpl->assign('mod_url', $mod_url);
$xoopsTpl->assign('cid', $cid);
$xoopsTpl->assign('can_add', ($global_perms & GPERM_INSERTABLE) && $cid);
$cats = $GLOBALS['cattree']->getChildTreeArray(0, 'title');
$xoopsTpl->assign('makethumb', $myalbum_makethumb);
$xoopsTpl->assign('lang_imagesize', _ALBM_CAPTION_IMAGEXYT);
$xoopsTpl->assign('lang_align', _ALIGN);
$xoopsTpl->assign('lang_add', _ADD);
$xoopsTpl->assign('lang_close', _CLOSE);
$xoopsTpl->assign('lang_left', _LEFT);
$xoopsTpl->assign('lang_center', _CENTER);
$xoopsTpl->assign('lang_right', _RIGHT);

if (count($cats) > 0) {
    $xoopsTpl->assign('lang_refresh', _ALBM_CAPTION_REFRESH);

    // WHERE clause for ext
    // $whr_ext = "ext IN ('" . implode( "','" , $myalbum_normal_exts ) . "')" ;
    $whr_ext          = '1';
    $select_is_normal = "ext IN ('" . implode("','", $myalbum_normal_exts) . "')";

    // select box for category
    $cat_options  = "<option value='0'>--</option>\n";
    $sql          = "SELECT cid,COUNT(lid) FROM $table_photos WHERE status>0 AND $whr_ext GROUP BY cid";
    $prs          = $xoopsDB->query($sql);
    $photo_counts = [];
    if ($xoopsDB->isResultSet($prs)) {
        while ([$c, $p] = $xoopsDB->fetchRow($prs)) {
            $photo_counts[$c] = $p;
        }
    } else {
        \trigger_error("Query Failed! SQL: $sql- Error: " . $xoopsDB->error(), E_USER_ERROR);
    }
    foreach ($cats as $cat) {
        $prefix      = str_replace('.', '--', mb_substr($cat['prefix'], 1));
        $photo_count = $photo_counts[$cat['cid']] ?? 0;
        if ($cid == $cat['cid']) {
            $cat_options .= "<option value='{$cat['cid']}' selected>$prefix{$cat['title']} ($photo_count)</option>\n";
        } else {
            $cat_options .= "<option value='{$cat['cid']}'>$prefix{$cat['title']} ($photo_count)</option>\n";
        }
    }
    $xoopsTpl->assign('cat_options', $cat_options);

    if ($cid > 0) {
        $xoopsTpl->assign('lang_addimage', _ADDIMAGE);

        $sql = "SELECT COUNT(*) FROM $table_photos WHERE cid='$cid' AND status>0 AND $whr_ext";
        $rs  = $xoopsDB->query($sql);
        if ($xoopsDB->isResultSet($rs)) {
            [$total] = $xoopsDB->fetchRow($rs);
        } else {
            \trigger_error("Query Failed! SQL: $sql- Error: " . $xoopsDB->error(), E_USER_ERROR);
        }
        if ($total > 0) {
            $start = Request::getInt('start', 0, 'GET');
            $sql   = "SELECT lid,cid,title,ext,submitter,res_x,res_y,$select_is_normal AS is_normal FROM $table_photos WHERE cid='$cid' AND status>0 AND $whr_ext ORDER BY date DESC LIMIT $start,$num";
            $prs   = $xoopsDB->query($sql);
            if (!$xoopsDB->isResultSet($prs)) {
                \trigger_error("Query Failed! SQL: $sql- Error: " . $xoopsDB->error(), E_USER_ERROR);
            }
            $xoopsTpl->assign('image_total', $total);
            $xoopsTpl->assign('lang_image', _IMAGE);
            $xoopsTpl->assign('lang_imagename', _IMAGENAME);

            if ($total > $num) {
                $nav = new \XoopsPageNav($total, $num, $start, 'start', "target=$target&amp;cid=$cid&amp;num=$num");
                $xoopsTpl->assign('pagenav', $nav->renderNav());
            }

            // use [siteimg] or [img]
            if (empty($myalbum_usesiteimg)) {
                // using links with XOOPS_URL
                $img_tag = 'img';
                $url_tag = 'url';
                $pdir    = $photos_url;
                $tdir    = $thumbs_url;
            } else {
                // using links without XOOPS_URL
                $img_tag = 'siteimg';
                $url_tag = 'siteurl';
                $pdir    = mb_substr($myalbum_photospath, 1);
                $tdir    = mb_substr($myalbum_thumbspath, 1);
            }

            $i = 1;
            while ([$lid, $cid, $title, $ext, $submitter, $res_x, $res_y, $is_normal] = $xoopsDB->fetchRow($prs)) {
                // Width of thumb
                if (!$is_normal) {
                    $width_spec = '';
                    $image_ext  = 'gif';
                } else {
                    $width_spec = "width='$myalbum_thumbsize'";
                    $image_ext  = $ext;
                    if ($myalbum_makethumb) {
                        [$width, $height, $type] = getimagesize("$thumbs_dir/$lid.$ext");
                        if ($width <= $myalbum_thumbsize) {
                            $width_spec = '';
                        }
                    }
                }

                $xcodel  = "[$url_tag=$pdir/{$lid}.{$ext}][$img_tag align=left]$tdir/{$lid}.{$image_ext}[/$img_tag][/$url_tag]";
                $xcodec  = "[$url_tag=$pdir/{$lid}.{$ext}][$img_tag]$tdir/{$lid}.{$image_ext}[/$img_tag][/$url_tag]";
                $xcoder  = "[$url_tag=$pdir/{$lid}.{$ext}][$img_tag align=right]$tdir/{$lid}.{$image_ext}[/$img_tag][/$url_tag]";
                $xcodebl = "[$img_tag align=left]$pdir/{$lid}.{$ext}[/$img_tag]";
                $xcodebc = "[$img_tag]$pdir/{$lid}.{$ext}[/$img_tag]";
                $xcodebr = "[$img_tag align=right]$pdir/{$lid}.{$ext}[/$img_tag]";
                $xoopsTpl->append(
                    'photos',
                    [
                        'lid'        => $lid,
                        'ext'        => $ext,
                        'res_x'      => $res_x,
                        'res_y'      => $res_y,
                        'nicename'   => $GLOBALS['myts']->htmlSpecialChars($title),
                        'src'        => "$thumbs_url/{$lid}.{$image_ext}",
                        'can_edit'   => ($global_perms & GPERM_EDITABLE) && ($my_uid == $submitter || $isadmin),
                        'can_delete' => ($global_perms & GPERM_DELETABLE) && ($my_uid == $submitter || $isadmin),
                        'width_spec' => $width_spec,
                        'xcodel'     => $xcodel,
                        'xcodec'     => $xcodec,
                        'xcoder'     => $xcoder,
                        'xcodebl'    => $xcodebl,
                        'xcodebc'    => $xcodebc,
                        'xcodebr'    => $xcodebr,
                        'is_normal'  => $is_normal,
                        'count'      => ++$i,
                    ]
                );
            }
        } else {
            $xoopsTpl->assign('image_total', 0);
        }
    }
    $xoopsTpl->assign('xsize', 600);
    $xoopsTpl->assign('ysize', 400);
} else {
    $xoopsTpl->assign('xsize', 400);
    $xoopsTpl->assign('ysize', 180);
}

$xoopsTpl->display("db:{$moduleDirName }_imagemanager.tpl");
exit;
