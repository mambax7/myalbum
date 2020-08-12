<?php
// ------------------------------------------------------------------------- //
//                      myAlbum-P - XOOPS photo album                        //
//                        <http://www.peak.ne.jp>                           //
// ------------------------------------------------------------------------- //
require_once __DIR__ . '/header.php';

if (!($global_perms & GPERM_RATEVOTE)) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['mydirname'] . '/index.php', 1, _NOPERM);
}

$lid = \Xmf\Request::getInt('lid', 0, 'GET');
/** @var  Myalbum\PhotosHandler $photosHandler */
$photosHandler = $helper->getHandler('Photos');
/** @var  Myalbum\VotedataHandler $votedataHandler */
$votedataHandler = $helper->getHandler('Votedata');
if (!$photo_obj = $photosHandler->get($lid)) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['mydirname'] . '/index.php', 2, _ALBM_NOMATCH);
}

if (\Xmf\Request::hasVar('submit', 'POST')) {
    $ratinguser = $my_uid;

    //Make sure only 1 anonymous from an IP in a single day.
    $anonwaitdays = 1;
    $ip           = getenv('REMOTE_ADDR');
    $lid          = \Xmf\Request::getInt('lid', 0, 'POST');
    $rating       = \Xmf\Request::getInt('rating', 0, 'POST');
    // Check if rating is valid
    if ($rating <= 0 || $rating > 10) {
        redirect_header($photo_obj->getRateURL(), 4, _ALBM_NORATING);
    }

    if (0 != $ratinguser) {
        // Check if Photo POSTER is voting
        $criteria = new \CriteriaCompo(new \Criteria('`lid`', $lid, '='));
        $criteria->add(new \Criteria('`submitter`', $ratinguser));

        if ($photosHandler->getCount($criteria)) {
            redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['mydirname'] . '/index.php', 4, _ALBM_CANTVOTEOWN);
        }

        $criteria = new \CriteriaCompo(new \Criteria('`lid`', $lid, '='));
        $criteria->add(new \Criteria('`ratinguser`', $ratinguser));

        // Check if REG user is trying to vote twice.
        if ($votedataHandler->getCount($criteria)) {
            redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['mydirname'] . '/index.php', 4, _ALBM_VOTEONCE2);
        }
    } else {
        // Check if ANONYMOUS user is trying to vote more than once per day.
        $yesterday = (time() - (86400 * $anonwaitdays));
        $criteria  = new \CriteriaCompo(new \Criteria('`ratingtimestamp`', $yesterday, '>'));
        $criteria->add(new \Criteria('`ratinguser`', 0));
        $criteria->add(new \Criteria('`ratinghostname`', $ip));
        // Check if REG user is trying to vote twice.
        if ($votedataHandler->getCount($criteria)) {
            redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['mydirname'] . '/index.php', 4, _ALBM_VOTEONCE2);
        }
    }

    // All is well.  Add to Line Item Rate to DB.
    $vote     = $votedataHandler->create();
    $datetime = time();
    $vote->setVar('lid', $lid);
    $vote->setVar('ratinguser', $ratinguser);
    $vote->setVar('rating', $rating);
    $vote->setVar('ratinghostname', $ip);
    $vote->setVar('ratingtimestamp', $datetime);
    $votedataHandler->insert($vote, true) || exit('DB error: INSERT votedata table');
    //All is well.  Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.
    Myalbum\Utility::updateRating($lid);
    $ratemessage = _ALBM_VOTEAPPRE . '<br>' . sprintf(_ALBM_THANKURATE, $xoopsConfig['sitename']);
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['mydirname'] . '/index.php', 2, $ratemessage);
} else {
    if (!mb_strpos($photo_obj->getRateURL(), $_SERVER['REQUEST_URI'])) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $photo_obj->getRateURL());
        exit(0);
    }

    $GLOBALS['xoopsOption']['template_main'] = "{$moduleDirName }_ratephoto.tpl";
    require $GLOBALS['xoops']->path('header.php');

    $GLOBALS['xoopsTpl']->assign('photo', Myalbum\Preview::getArrayForPhotoAssign($photo_obj));

    require_once __DIR__ . '/include/assign_globals.php';
    $GLOBALS['xoopsTpl']->assign($myalbum_assign_globals);

    $GLOBALS['xoopsTpl']->assign('lang_voteonce', _ALBM_VOTEONCE);
    $GLOBALS['xoopsTpl']->assign('lang_ratingscale', _ALBM_RATINGSCALE);
    $GLOBALS['xoopsTpl']->assign('lang_beobjective', _ALBM_BEOBJECTIVE);
    $GLOBALS['xoopsTpl']->assign('lang_donotvote', _ALBM_DONOTVOTE);
    $GLOBALS['xoopsTpl']->assign('lang_rateit', _ALBM_RATEIT);
    $GLOBALS['xoopsTpl']->assign('lang_cancel', _CANCEL);
    $GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['myalbumModuleConfig']);
    $GLOBALS['xoopsTpl']->assign('mydirname', $GLOBALS['mydirname']);

    require $GLOBALS['xoops']->path('footer.php');
}
