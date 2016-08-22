<?php
// ------------------------------------------------------------------------- //
//                XOOPS - PHP Content Management System                      //
//                       <http://xoops.org/>                             //
// ------------------------------------------------------------------------- //
// Based on:                                     //
// myPHPNUKE Web Portal System - http://myphpnuke.com/               //
// PHP-NUKE Web Portal System - http://phpnuke.org/              //
// Thatware - http://thatware.org/                       //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------- //
include dirname(dirname(__DIR__)) . '/mainfile.php';
$moduleDirName = basename(__DIR__);

$GLOBALS['mydirname'] = basename(__DIR__);
include XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/read_configs.php";
include XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/get_perms.php";
include_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/functions.php";
include_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/draw_functions.php";
include_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/class/myuploader.php";

$GLOBALS['myts'] = MyTextSanitizer::getInstance();

$moduleHandler                  = xoops_getHandler('module');
$configHandler                 = xoops_getHandler('config');
$GLOBALS['myalbumModule']       = $moduleHandler->getByDirname($GLOBALS['mydirname']);
$GLOBALS['myalbumModuleConfig'] = $configHandler->getConfigList($GLOBALS['myalbumModule']->getVar('mid'));
$GLOBALS['myalbum_mid']         = $GLOBALS['myalbumModule']->getVar('mid');
$GLOBALS['photos_dir']          = XOOPS_ROOT_PATH . $GLOBALS['myalbumModuleConfig']['myalbum_photospath'];
$GLOBALS['thumbs_dir']          = XOOPS_ROOT_PATH . $GLOBALS['myalbumModuleConfig']['myalbum_thumbspath'];
$GLOBALS['photos_url']          = XOOPS_URL . $GLOBALS['myalbumModuleConfig']['myalbum_photospath'];
$GLOBALS['thumbs_url']          = XOOPS_URL . $GLOBALS['myalbumModuleConfig']['myalbum_thumbspath'];

xoops_load('pagenav');
xoops_load('xoopslists');
xoops_load('xoopsformloader');

include_once $GLOBALS['xoops']->path('class' . DS . 'xoopsmailer.php');
include_once $GLOBALS['xoops']->path('class' . DS . 'tree.php');

$catHandler        = xoops_getModuleHandler('cat');
$cats               = $catHandler->getObjects(null, true);
$GLOBALS['cattree'] = new XoopsObjectTree($cats, 'cid', 'pid', 0);

xoops_loadLanguage('main', $moduleDirName);

if ($GLOBALS['myalbumModuleConfig']['tag']) {
    include_once $GLOBALS['xoops']->path('modules' . DS . 'tag' . DS . 'include' . DS . 'formtag.php');
}

extract($GLOBALS['myalbumModuleConfig']);

if (!isset($GLOBALS['xoopsTpl']) || !is_object($GLOBALS['xoopsTpl'])) {
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    $GLOBALS['xoopsTpl'] = new XoopsTpl();
}

include XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/assign_globals.php";