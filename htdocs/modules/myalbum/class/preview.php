<?php

/**
 * Class MyalbumUtilities
 */
class MyalbumPreview extends XoopsObject
{
    // for older files
    public static function header()
    {
        global $mod_url, $moduleDirName;

        $tpl = new XoopsTpl();
        $tpl->assign(array('mod_url' => $mod_url));
        $tpl->display("db:{$moduleDirName }_header.tpl");
    }

    // for older files
    public static function footer()
    {
        global $mod_copyright, $moduleDirName;

        $tpl = new XoopsTpl();
        $tpl->assign(array('mod_copyright' => $mod_copyright));
        $tpl->display("db:{$moduleDirName }_footer.tpl");
    }

    // returns appropriate name from uid
    /**
     * @param $uid
     *
     * @return string
     */
    public static function getNameFromUid($uid)
    {
        global $myalbum_nameoruname;

        if ($uid > 0) {
            $memberHandler = xoops_getHandler('member');
            $poster        = $memberHandler->getUser($uid);

            if (is_object($poster)) {
                if ($myalbum_nameoruname === 'uname') {
                    $name = $poster->uname();
                } else {
                    $name = htmlspecialchars($poster->name());
                    if ($name == '') {
                        $name = $poster->uname();
                    }
                }
            } else {
                $name = _ALBM_CAPTION_GUESTNAME;
            }
        } else {
            $name = _ALBM_CAPTION_GUESTNAME;
        }

        return $name;
    }

    // Get photo's array to assign into template (heavy version)
    /**
     * @param      $photo
     * @param bool $summary
     *
     * @return array
     */
    public static function getArrayForPhotoAssign($photo, $summary = false)
    {
        global $my_uid, $isadmin, $global_perms;
        global $photos_url, $thumbs_url, $thumbs_dir, $mod_url, $mod_path;
        global $myalbum_makethumb, $myalbum_thumbsize, $myalbum_popular, $myalbum_newdays, $myalbum_normal_exts;

        /** @var MyalbumPhotosHandler $photosHandler */
        $photosHandler   = xoops_getModuleHandler('photos', $GLOBALS['mydirname']);
        /** @var MyalbumTextHandler $textHandler */
        $textHandler     = xoops_getModuleHandler('text', $GLOBALS['mydirname']);
        /** @var MyalbumCatHandler $catHandler */
        $catHandler      = xoops_getModuleHandler('cat', $GLOBALS['mydirname']);
        /** @var MyalbumVotedataHandler $votedataHandler */
        $votedataHandler = xoops_getModuleHandler('votedata', $GLOBALS['mydirname']);
        /** @var MyalbumCommentsHandler $commentsHandler */
        $commentsHandler = xoops_getModuleHandler('comments', $GLOBALS['mydirname']);

        extract($photo->toArray(true));
        $text = $textHandler->get($photo->getVar('lid'));
        $cat  = $catHandler->get($photo->getVar('cid'));
        $ext  = $photo->vars['ext']['value'];

        if (in_array(strtolower($ext), $myalbum_normal_exts)) {
            $imgsrc_thumb    = $photo->getThumbsURL();
            $imgsrc_photo    = $photo->getPhotoURL();
            $ahref_photo     = $photo->getPhotoURL();
            $is_normal_image = true;

            // Width of thumb
            $width_spec = "width='$myalbum_thumbsize'";
            if ($myalbum_makethumb) {
                list($width, $height, $type) = getimagesize("$thumbs_dir/$lid.$ext");
                // if thumb images was made, 'width' and 'height' will not set.
                if ($width <= $myalbum_thumbsize) {
                    $width_spec = '';
                }
            }
        } else {
            $imgsrc_thumb    = $photo->getThumbsURL();
            $imgsrc_photo    = $photo->getPhotoURL();
            $ahref_photo     = $photo->getPhotoURL();
            $is_normal_image = false;
            $width_spec      = '';
        }

        // Voting stats
        if ($rating > 0) {
            if ($votes == 1) {
                $votestring = _ALBM_ONEVOTE;
            } else {
                $votestring = sprintf(_ALBM_NUMVOTES, $votes);
            }
            $info_votes = number_format($rating, 2) . " ($votestring)";
        } else {
            $info_votes = '0.00 (' . sprintf(_ALBM_NUMVOTES, 0) . ')';
        }

        // Submitter's name
        $submitter_name = static::getNameFromUid($submitter);

        // Category's title
        $cat_title = !is_object($cat) ? '' : $cat->getVar('title');

        // Summarize description
        if (is_object($text)) {
            if ($summary) {
                $description = extractSummary($text->getVar('description'));
            } else {
                $description = $text->getVar('description');
            }
        } else {
            $description = '';
        }

        if (!empty($_POST['preview'])) {
            $description = $GLOBALS['myts']->stripSlashesGPC($_POST['desc_text']);
            $title       = $GLOBALS['myts']->stripSlashesGPC($_POST['title']);
        }

        if ($GLOBALS['myalbumModuleConfig']['tag']) {
            include_once XOOPS_ROOT_PATH . '/modules/tag/include/tagbar.php';
            $tagbar = tagBar($lid, $cid);
        } else {
            $tagbar = array();
        }

        return array(
            'tagbar'          => $tagbar,
            'lid'             => $lid,
            'cid'             => $cid,
            'ext'             => $ext,
            'res_x'           => $res_x,
            'res_y'           => $res_y,
            'window_x'        => $res_x + 16,
            'window_y'        => $res_y + 16,
            'title'           => $GLOBALS['myts']->htmlSpecialChars($title),
            'datetime'        => formatTimestamp($date, 'm'),
            'description'     => $GLOBALS['myts']->displayTarea($description, 1, 1, 1, 1, 1, 1),
            'imgsrc_thumb'    => $imgsrc_thumb,
            'imgsrc_photo'    => $imgsrc_photo,
            'ahref_photo'     => $ahref_photo,
            'width_spec'      => $width_spec,
            'can_edit'        => ($global_perms & GPERM_EDITABLE) && ($my_uid == $submitter || $isadmin),
            'can_delete'      => ($global_perms & GPERM_DELETABLE) && ($my_uid == $submitter || $isadmin),
            'submitter'       => $submitter,
            'submitter_name'  => $submitter_name,
            'hits'            => $hits,
            'rating'          => $rating,
            'rank'            => floor($rating - 0.001),
            'votes'           => $votes,
            'info_votes'      => $info_votes,
            'comments'        => $comments,
            'is_normal_image' => $is_normal_image,
            'is_newphoto'     => $date > time() - 86400 * $myalbum_newdays && $status == 1,
            'is_updatedphoto' => $date > time() - 86400 * $myalbum_newdays && $status == 2,
            'is_popularphoto' => $hits >= $myalbum_popular,
            'info_morephotos' => sprintf(_ALBM_MOREPHOTOS, $submitter_name),
            'cat_title'       => $GLOBALS['myts']->htmlSpecialChars($cat_title),
            'status'          => $status
        );
    }

    // Get photo's array to assign into template (light version)
    /**
     * @param      $photo
     * @param bool $summary
     *
     * @return array
     */
    public static function getArrayForPhotoAssignLight($photo, $summary = false)
    {
        global $my_uid, $isadmin, $global_perms;
        global $photos_url, $thumbs_url, $thumbs_dir;
        global $myalbum_makethumb, $myalbum_thumbsize, $myalbum_normal_exts;

        /** @var MyalbumPhotosHandler $photosHandler */
        $photosHandler   = xoops_getModuleHandler('photos', $GLOBALS['mydirname']);
        /** @var MyalbumTextHandler $textHandler */
        $textHandler     = xoops_getModuleHandler('text', $GLOBALS['mydirname']);
        /** @var MyalbumCatHandler $catHandler */
        $catHandler      = xoops_getModuleHandler('cat', $GLOBALS['mydirname']);
        /** @var MyalbumVotedataHandler $votedataHandler */
        $votedataHandler = xoops_getModuleHandler('votedata', $GLOBALS['mydirname']);
        /** @var MyalbumCommentsHandler $commentsHandler */
        $commentsHandler = xoops_getModuleHandler('comments', $GLOBALS['mydirname']);

        extract($photo->toArray(true));
        $text = $textHandler->get($photo->getVar('lid'));
        $cat  = $catHandler->get($photo->getVar('cid'));

        if (in_array(strtolower($ext), $myalbum_normal_exts)) {
            $imgsrc_thumb    = $photo->getThumbsURL();
            $imgsrc_photo    = $photo->getPhotoURL();
            $is_normal_image = true;
            // Width of thumb
            $width_spec = "width='$myalbum_thumbsize'";
            if ($myalbum_makethumb && $ext !== 'gif') {
                // if thumb images was made, 'width' and 'height' will not set.
                $width_spec = '';
            }
        } else {
            $imgsrc_thumb    = $photo->getThumbsURL();
            $imgsrc_photo    = $photo->getPhotoURL();
            $is_normal_image = false;
            $width_spec      = '';
        }

        if ($GLOBALS['myalbumModuleConfig']['tag']) {
            include_once XOOPS_ROOT_PATH . '/modules/tag/include/tagbar.php';
            $tagbar = tagBar($lid, $cid);
        } else {
            $tagbar = array();
        }

        return array(
            'tagbar'          => $tagbar,
            'lid'             => $lid,
            'cid'             => $cid,
            'ext'             => $ext,
            'res_x'           => $res_x,
            'res_y'           => $res_y,
            'window_x'        => $res_x + 16,
            'window_y'        => $res_y + 16,
            'title'           => $GLOBALS['myts']->htmlSpecialChars($title),
            'imgsrc_thumb'    => $imgsrc_thumb,
            'imgsrc_photo'    => $imgsrc_photo,
            'width_spec'      => $width_spec,
            'can_edit'        => ($global_perms & GPERM_EDITABLE) && ($my_uid == $submitter || $isadmin),
            'can_delete'      => ($global_perms & GPERM_DELETABLE) && ($my_uid == $submitter || $isadmin),
            'hits'            => $hits,
            'rating'          => $rating,
            'rank'            => floor($rating - 0.001),
            'votes'           => $votes,
            'comments'        => $comments,
            'is_normal_image' => $is_normal_image
        );
    }

    // get list of sub categories in header space
    /**
     * @param $parent_id
     * @param $cattree
     *
     * @return array
     */
    public static function getSubCategories($parent_id, $cattree)
    {
        $ret      = array();
        $criteria = new Criteria('`status`', '0', '>');
        $criterib = new Criteria('`pid`', $parent_id, '=');
        $criterib->setSort('cid');
        $criterib->setOrder('DESC');

        /** @var MyalbumTextHandler $textHandler */
        $catHandler = xoops_getModuleHandler('cat', $GLOBALS['mydirname']);

        $cats = $catHandler->getObjects($criterib, true);

        foreach ($cats as $cid => $cat) {
            extract($cat->toArray());
            // Show first child of this category
            $subcat = array();
            $arr    = $GLOBALS['cattree']->getFirstChild($cid);
            foreach ($arr as $child) {
                $subcat[] = array(
                    'cid'              => $child->getVar('cid'),
                    'title'            => $child->getVar('title'),
                    'weight'           => $child->getVar('weight'),
                    'photo_small_sum'  => MyalbumUtilities::getCategoryCount($child->getVar('cid'), $criteria),
                    'number_of_subcat' => count($GLOBALS['cattree']->getFirstChild($child->getVar('cid')))
                );
            }

            // Category's banner default
            if ($imgurl === 'http://') {
                $imgurl = '';
            }

            // Total sum of photos
            $cids = array();
            foreach ($GLOBALS['cattree']->getAllChild($cid, array()) as $children) {
                $cids[] = $children->getVar('cid');
            }

            array_push($cids, $cid);

            $photo_total_sum = MyalbumUtilities::getTotalCount($cids, $criteria);

            $ret[] = array(
                'cid'             => $cid,
                'imgurl'          => $GLOBALS['myts']->htmlSpecialChars($imgurl),
                'photo_small_sum' => MyalbumUtilities::getCategoryCount($cid, $criteria),
                'photo_total_sum' => $photo_total_sum,
                'title'           => $title,
                'weight'          => $weight,
                'subcategories'   => $subcat
            );
        }

        return $ret;
    }

    // get attributes of <img> for preview image
    /**
     * @param $preview_name
     *
     * @return array
     */
    public static function getImageAttribsForPreview($preview_name)
    {
        global $photos_url, $mod_url, $mod_path, $myalbum_normal_exts, $myalbum_thumbsize;

        $ext = substr(strrchr($preview_name, '.'), 1);

        if (in_array(strtolower($ext), $myalbum_normal_exts)) {
            return array("$photos_url/$preview_name", "width='$myalbum_thumbsize'", "$photos_url/$preview_name");
        } else {
            if (file_exists("$mod_path/assets/images/icons/$ext.gif")) {
                return array("$mod_url/assets/images/icons/mp3.gif", '', "$photos_url/$preview_name");
            } else {
                return array("$mod_url/assets/images/icons/default.gif", '', "$photos_url/$preview_name");
            }
        }
    }
}