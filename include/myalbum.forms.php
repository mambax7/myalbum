<?php declare(strict_types=1);

use Xmf\Request;
use XoopsModules\Myalbum\{
    Helper,
    GroupPermForm,
    Utility
};
use XoopsModules\Tag\FormTag;

function myalbum_admin_form_admission(): string
{
    $helper = Helper::getInstance();
    // Make form objects
    $form       = new \XoopsThemeForm(_ALBM_PHOTOBATCHUPLOAD, 'batchupload', 'batch.php');
    $title_text = new \XoopsFormText('', 'title', 50, 255, $GLOBALS['title4edit']);
    $titleTray  = new \XoopsFormElementTray(_AM_TH_TITLE, '<br><br>');
    $titleTray->addElement($title_text);
    $titleTray->addElement(new \XoopsFormLabel('', _ALBM_BATCHBLANK));
    //$cat_select       = new \XoopsFormLabel(_AM_TH_CATEGORIES, $GLOBALS['cattree']->makeSelBox('cid', 'title', '--', null, false));
    $cat_select       = $GLOBALS['cattree']->makeSelectElement('cid', 'title', '--', null, false, 0, '', \_AM_TH_CATEGORIES);
    $submitter_select = new \XoopsFormSelectUser(_AM_TH_SUBMITTER, 'submitter', false, $GLOBALS['submitter']);
    $date_text        = new \XoopsFormText(_AM_TH_DATE, 'post_date', 20, 20, formatTimestamp(time(), _ALBM_DTFMT_YMDHI));
    $dirTray          = new \XoopsFormElementTray(_ALBM_TEXT_DIRECTORY, '<br><br>');
    $dir_text         = new \XoopsFormText(_ALBM_PHOTOPATH, 'dir', 50, 255, $GLOBALS['dir4edit']);
    $dirTray->addElement($dir_text);
    $dirTray->addElement(new \XoopsFormLabel(_ALBM_DESC_PHOTOPATH));
    $html_configs           = [];
    $html_configs['name']   = 'desc_text';
    $html_configs['value']  = $GLOBALS['desc4edit'];
    $html_configs['rows']   = 35;
    $html_configs['cols']   = 60;
    $html_configs['width']  = '100%';
    $html_configs['height'] = '400px';
    $html_configs['editor'] = $helper->getConfig('editor');
    $desc_tarea             = new \XoopsFormEditor(_ALBM_PHOTODESC, $html_configs['name'], $html_configs);
    $submit_button          = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
    $form->addElement($titleTray);
    $form->addElement($desc_tarea);
    $form->addElement($cat_select);
    $form->addElement($dirTray);
    $form->addElement($submitter_select);
    $form->addElement($date_text);
    $form->addElement($submit_button);
    $form->setRequired($dir_text);

    return $form->render();
}

function myalbum_admin_form_export(): string
{
    $sql = 'SELECT c.imgcat_id,c.imgcat_name,c.imgcat_storetype,COUNT(i.image_id) AS imgcat_sum FROM ' . $GLOBALS['xoopsDB']->prefix('imagecategory') . ' c NATURAL LEFT JOIN ' . $GLOBALS['xoopsDB']->prefix('image') . ' i GROUP BY c.imgcat_id ORDER BY c.imgcat_weight';
    $irs            = $GLOBALS['xoopsDB']->query($sql);
    $imgcat_options = '';

    if ($GLOBALS['xoopsDB']->isResultSet($irs)) {
        while (list($imgcat_id, $imgcat_name, $imgcat_storetype, $imgcat_sum) = $GLOBALS['xoopsDB']->fetchRow($irs)) {
            $imgcat_options .= "<option value='$imgcat_id'>$imgcat_storetype : $imgcat_name ($imgcat_sum)</option>\n";
        }
    } else {
        \trigger_error("Query Failed! SQL: $sql- Error: " . $GLOBALS['xoopsDB']->error(), E_USER_ERROR);
    }

    // Options for Selecting a category in myAlbum-P
    $myalbum_cat_options = Utility::getCategoryOptions('title', 0, '--', '----');

    return '<h4>' . _AM_FMT_EXPORTTOIMAGEMANAGER . "</h4>
<form name='ImageManager' action='export.php' method='POST'>
<select name='cid'>
    $myalbum_cat_options
</select>
" . _AM_FMT_EXPORTIMSRCCAT . "
&nbsp; -> &nbsp;
<select name='imgcat_id'>
    $imgcat_options
</select>
" . _AM_FMT_EXPORTIMDSTCAT . "
<br>
<br>
<input type='checkbox' name='use_thumb' value='1' checked>" . _AM_CB_EXPORTTHUMB . "
<br>
<br>
<input type='submit' name='imagemanager_export' value='" . _GO . "' onclick='return confirm(\"" . _AM_MB_EXPORTCONFIRM . "\");'>
</form>\n";
}

function myalbum_admin_form_groups(): string
{
    global $xoopsModule;

    $global_perms_array = [
        GPERM_INSERTABLE                     => _ALBM_GPERM_G_INSERTABLE,
        GPERM_SUPERINSERT | GPERM_INSERTABLE => _ALBM_GPERM_G_SUPERINSERT,
        //      GPERM_EDITABLE => _ALBM_GPERM_G_EDITABLE ,
        GPERM_SUPEREDIT | GPERM_EDITABLE     => _ALBM_GPERM_G_SUPEREDIT,
        //      GPERM_DELETABLE => _ALBM_GPERM_G_DELETABLE ,
        GPERM_SUPERDELETE | GPERM_DELETABLE  => _ALBM_GPERM_G_SUPERDELETE,
        GPERM_RATEVIEW                       => _ALBM_GPERM_G_RATEVIEW,
        GPERM_RATEVOTE | GPERM_RATEVIEW      => _ALBM_GPERM_G_RATEVOTE,
    ];

    $form = new GroupPermForm('', $xoopsModule->mid(), 'myalbum_global', _AM_ALBM_GROUPPERM_GLOBALDESC);
    foreach ($global_perms_array as $perm_id => $perm_name) {
        $form->addItem($perm_id, $perm_name);
    }

    return $form->render();
}

function myalbum_admin_form_import_myalbum(): string
{
    /** @var \XoopsModuleHandler $moduleHandler */
    $moduleHandler = xoops_getHandler('module');
    $sql           =
    $mrs = $GLOBALS['xoopsDB']->query('SELECT dirname FROM ' . $GLOBALS['xoopsDB']->prefix('modules') . " WHERE dirname LIKE 'myalbum%'");
    $frm           = '';
    if ($GLOBALS['xoopsDB']->isResultSet($mrs)) {
        while ([$src_dirname] = $GLOBALS['xoopsDB']->fetchRow($mrs)) {
            if ($GLOBALS['mydirname'] == $src_dirname) {
                continue;
            }

            /** @var \XoopsModule $module */
            $module = $moduleHandler->getByDirname($src_dirname);
            if (!is_object($module)) {
                continue;
            }

            if (!$GLOBALS['xoopsUser']->isAdmin($module->getVar('mid'))) {
                continue;
            }

            $myalbum_cat_options = Utility::getCategoryOptions('title', 0, '--', '----', $GLOBALS['xoopsDB']->prefix("{$src_dirname}_cat"), $GLOBALS['xoopsDB']->prefix("{$src_dirname}_photos"));

            $frm .= '<p>
                <h4>' . sprintf(_AM_FMT_IMPORTFROMMYALBUMP, (string)$module->name()) . "</h4>
                <form name='$src_dirname' action='import.php' method='POST'>
                <input type='hidden' name='src_dirname' value='$src_dirname'>
                <input type='radio' name='copyormove' value='copy' checked>" . _AM_RADIO_IMPORTCOPY . " &nbsp;
                <input type='radio' name='copyormove' value='move'>" . _AM_RADIO_IMPORTMOVE . "<br><br>
                <!-- <input type='checkbox' name='import_recursively'>" . _AM_CB_IMPORTRECURSIVELY . "<br><br> -->
                <select name='cid'>
                    $myalbum_cat_options
                </select>
                <input type='submit' name='myalbum_import' value='" . _GO . "' onclick='return confirm(\"" . _AM_MB_IMPORTCONFIRM . "\");'>
                </form>\n";

            $frm .= '<br></p>';
        }
    } else {
        \trigger_error("Query Failed! SQL: $sql- Error: " . $GLOBALS['xoopsDB']->error(), E_USER_ERROR);
    }

    return $frm;
}

function myalbum_admin_form_import_imagemanager(): string
{
    /** @var \XoopsGroupPermHandler $grouppermHandler */
    $grouppermHandler = xoops_getHandler('groupperm');
    $frm              = '';
    if ($grouppermHandler->checkRight('system_admin', XOOPS_SYSTEM_IMAGE, $GLOBALS['xoopsUser']->getGroups())) {
        // only when user has admin right of system 'imagemanager'
        $sql            = 'SELECT c.imgcat_id,c.imgcat_name,COUNT(i.image_id) AS imgcat_sum FROM ' . $GLOBALS['xoopsDB']->prefix('imagecategory') . ' c NATURAL LEFT JOIN ' . $GLOBALS['xoopsDB']->prefix('image') . ' i GROUP BY c.imgcat_id ORDER BY c.imgcat_weight';
        $irs = $GLOBALS['xoopsDB']->query($sql);
        $imgcat_options = '';
        if ($GLOBALS['xoopsDB']->isResultSet($irs)) {
            while ([$imgcat_id, $imgcat_name, $imgcat_sum] = $GLOBALS['xoopsDB']->fetchRow($irs)) {
                $imgcat_options .= "<option value='$imgcat_id'>$imgcat_name ($imgcat_sum)</option>\n";
            }
        } else {
            \trigger_error("Query Failed! SQL: $sql- Error: " . $GLOBALS['xoopsDB']->error(), E_USER_ERROR);
        }
        $frm .= '<p>
                <h4>' . _AM_FMT_IMPORTFROMIMAGEMANAGER . "</h4>
                <form name='ImageManager' action='import.php' method='POST'>
                <select name='imgcat_id'>
                    $imgcat_options
                </select>
                <input type='submit' name='imagemanager_import' value='" . _GO . "' onclick='return confirm(\"" . _AM_MB_IMPORTCONFIRM . "\");'>
                </form>\n";
        $frm .= '<br></p>';
    }

    return $frm;
}

/**
 * @param $cat_array
 * @param $form_title
 * @param $action
 * @return string
 */
function myalbum_admin_form_display_edit($cat_array, $form_title, $action): string
{
    global $cattree;

    extract($cat_array);

    // Beggining of XoopsForm
    $form = new \XoopsThemeForm($form_title, 'MainForm', '');

    // Hidden
    $form->addElement(new \XoopsFormHidden('action', $action));
    $form->addElement(new \XoopsFormHidden('cid', $cid));

    // Title
    $form->addElement(new \XoopsFormText(_AM_CAT_TH_TITLE, 'title', 30, 50, $GLOBALS['myts']->htmlSpecialChars($title)), true);

    // Weight
    $form->addElement(new \XoopsFormText(_AM_CAT_TH_WEIGHT, 'weight', 30, 50, $weight));

    // Image URL
    $form->addElement(new \XoopsFormText(_AM_CAT_TH_IMGURL, 'imgurl', 50, 150, $GLOBALS['myts']->htmlSpecialChars($imgurl)));

    // Parent Category
    //$form->addElement(new \XoopsFormLabel(_ALBM_PHOTOCAT, $GLOBALS['cattree']->makeSelBox('pid', 'title', '--', $pid, true)));
    $form->addElement($GLOBALS['cattree']->makeSelectElement('pid', 'title', '--', $pid, true, 0, '', \_ALBM_PHOTOCAT));

    // Buttons
    $buttonTray = new \XoopsFormElementTray('', '&nbsp;');
    $buttonTray->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $buttonTray->addElement(new \XoopsFormButton('', 'reset', _CANCEL, 'reset'));
    $form->addElement($buttonTray);

    // End of XoopsForm
    return $form->render();
}

/**
 * @param string $caller
 * @param        $photo
 * @param        $lid
 * @return string
 */
function myalbum_user_form_submit(string $caller, $photo, $lid): string
{
    $helper = Helper::getInstance();
    // Show the form
    extract($GLOBALS['myalbumModuleConfig']);
    $form        = new \XoopsThemeForm(_ALBM_PHOTOUPLOAD, 'uploadphoto', "submit.php?caller=$caller");
    $pixels_text = "$myalbum_width x $myalbum_height";
    if (isset($myalbum_canresize) && $myalbum_canresize) {
        $pixels_text .= ' (auto resize)';
    }
    $pixels_label = new \XoopsFormLabel(_ALBM_MAXPIXEL, $pixels_text);
    $size_label   = new \XoopsFormLabel(_ALBM_MAXSIZE, $myalbum_fsize . (empty($file_uploads_off) ? '' : ' &nbsp; <strong>"file_uploads" off</strong>'));
    $form->setExtra("enctype='multipart/form-data'");

    $title_text = new \XoopsFormText(_ALBM_PHOTOTITLE, 'title', 50, 255, $photo['title']);

    //$cat_select = new \XoopsFormLabel(_ALBM_PHOTOCAT, $GLOBALS['cattree']->makeSelBox('cid', 'title', '--', \Xmf\Request::getInt('cid', null, 'REQUEST'), false));
    $cat_select = $GLOBALS['cattree']->makeSelectElement('cid', 'title', '--', \Xmf\Request::getInt('cid', null, 'REQUEST'), false, 0, '', \_ALBM_PHOTOCAT);

    $html_configs           = [];
    $html_configs['name']   = 'desc_text';
    $html_configs['value']  = $photo['description'];
    $html_configs['rows']   = 35;
    $html_configs['cols']   = 60;
    $html_configs['width']  = '100%';
    $html_configs['height'] = '400px';
    $html_configs['editor'] = $helper->getConfig('editor');
    $desc_tarea             = new \XoopsFormEditor(_ALBM_PHOTODESC, $html_configs['name'], $html_configs);

    $file_form = new \XoopsFormFile(_ALBM_SELECTFILE, 'photofile', $myalbum_fsize);
    $file_form->setExtra("size='70'");
    if (isset($myalbum_canrotate) && $myalbum_canrotate) {
        $rotate_radio = new \XoopsFormRadio(_ALBM_RADIO_ROTATETITLE, 'rotate', 'rot0');
        $rotate_radio->addOption('rot0', _ALBM_RADIO_ROTATE0 . ' &nbsp; ');
        $rotate_radio->addOption('rot90', "<img src='assets/images/icon_rotate90.gif' alt='" . _ALBM_RADIO_ROTATE90 . "' title='" . _ALBM_RADIO_ROTATE90 . "'> &nbsp; ");
        $rotate_radio->addOption('rot180', "<img src='assets/images/icon_rotate180.gif' alt='" . _ALBM_RADIO_ROTATE180 . "' title='" . _ALBM_RADIO_ROTATE180 . "'> &nbsp; ");
        $rotate_radio->addOption('rot270', "<img src='assets/images/icon_rotate270.gif' alt='" . _ALBM_RADIO_ROTATE270 . "' title='" . _ALBM_RADIO_ROTATE270 . "'> &nbsp; ");
    }
    $op_hidden      = new \XoopsFormHidden('op', 'submit');
    $counter_hidden = new \XoopsFormHidden('fieldCounter', 1);
    if (!isset($preview_name)) {
        $preview_name = '';
    }
    $preview_hidden = new \XoopsFormHidden('preview_name', htmlspecialchars($preview_name, ENT_QUOTES | ENT_HTML5));

    $submit_button  = new \XoopsFormButton('', 'submit', _SUBMIT, 'submit');
    $preview_button = new \XoopsFormButton('', 'preview', _PREVIEW, 'submit');
    $reset_button   = new \XoopsFormButton('', 'reset', _CANCEL, 'reset');
    $submitTray     = new \XoopsFormElementTray('');
    if ('imagemanager' !== $caller) {
        $submitTray->addElement($preview_button);
    }
    $submitTray->addElement($submit_button);
    $submitTray->addElement($reset_button);

    $form->addElement($pixels_label);
    $form->addElement($size_label);
    $form->addElement($title_text);
    $form->addElement($desc_tarea);
    if ($helper->getConfig('tag') && \class_exists(\XoopsModules\Tag\FormTag::class)) {
        $form->addElement(new \XoopsModules\Tag\FormTag('tags', 35, 255, $lid));
    }
    $form->addElement($cat_select);
    $form->setRequired($cat_select);
    $form->addElement($file_form);
    if (isset($myalbum_canrotate) && $myalbum_canrotate) {
        $form->addElement($rotate_radio);
    }
    $form->addElement($preview_hidden);
    $form->addElement($counter_hidden);
    $form->addElement($op_hidden);
    $form->addElement($submitTray);

    // $form->setRequired( $file_form ) ;
    return $form->render();
}
