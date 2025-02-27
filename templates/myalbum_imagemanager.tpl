<!DOCTYPE html>
<html xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>">
    <meta http-equiv="content-language" content="<{$xoops_langcode}>">
    <title><{$sitename}> <{$lang_imgmanager}></title>
    <script type="text/javascript">
        <!--//
        function appendCode(addCode) {
            var targetDom = window.opener.xoopsGetElementById('<{$target}>');
            if (targetDom.createTextRange && targetDom.caretPos) {
                var caretPos = targetDom.caretPos;
                caretPos.text = caretPos.text.charAt(caretPos.text.length - 1)
                === ' ' ? addCode + ' ' : addCode;
            } else if (targetDom.getSelection && targetDom.caretPos) {
                var caretPos = targetDom.caretPos;
                caretPos.text = caretPos.text.charat(caretPos.text.length - 1)
                === ' ' ? addCode + ' ' : addCode;
            } else {
                targetDom.value = targetDom.value + addCode;
            }
//            return;
        }
        //-->
    </script>
    <style type="text/css" media="all">
        body {
            margin: 0;
        }

        img {
            border: 0;
        }

        table {
            width: 100%;
            margin: 0;
        }

        a:link {
            color: #3a76d6;
            font-weight: bold;
            background-color: transparent;
        }

        a:visited {
            color: #9eb2d6;
            font-weight: bold;
            background-color: transparent;
        }

        a:hover {
            color: #e18a00;
            background-color: transparent;
        }

        table td {
            background-color: white;
            font-size: 12px;
            padding: 0;
            border-width: 0;
            vertical-align: top;
            font-family: Verdana, Arial, Helvetica, sans-serif;
        }

        table#imagenav td {
            vertical-align: bottom;
            padding: 5px;
        }

        table#imagemain td {
            border-right: 1px solid silver;
            border-bottom: 1px solid silver;
            padding: 5px;
            vertical-align: middle;
        }

        table#imagemain th {
            border: 0;
            background-color: #2F5376;
            color: white;
            font-size: 12px;
            padding: 5px;
            vertical-align: top;
            text-align: center;
            font-family: Verdana, Arial, Helvetica, sans-serif;
        }

        table#header td {
            width: 100%;
            background-color: #2F5376;
            vertical-align: middle;
        }

        table#header td#headerbar {
            border-bottom: 1px solid silver;
            background-color: #dddddd;
        }

        div#pagenav {
            text-align: center;
        }

        div#footer {
            text-align: right;
            padding: 5px;
        }
    </style>
</head>

<{strip}>
    <body onload="window.resizeTo(<{$xsize}>, <{$ysize}>);">
    <table id="header" cellspacing="0">
        <tr>
            <td><a href="<{$xoops_url}>/"><img src="<{$xoops_url}>/images/logo.gif" width="148" height="80" alt=""></a>
            </td>
            <td></td>
        </tr>
        <tr>
            <td id="headerbar" colspan="2"></td>
        </tr>
    </table>

    <form action="<{$xoops_url}>/imagemanager.php" method="get">
        <table cellspacing="0" id="imagenav">
            <tr>
                <td>
                    <label>
                        <select name="cid" onchange="submit();"><{$cat_options}></select>
                    </label>
                    <input type="hidden" name="target" value="<{$target}>">
                    <input type="submit" value="<{$lang_refresh}>">
                </td>
                <{if $can_add}>
                    <td align="right"><input type="button" value="<{$lang_addimage}>"
                                             onclick='window.open("<{$mod_url}>/submit.php?cid=<{$cid}>&amp;caller=imagemanager","submitphoto","WIDTH=600,HEIGHT=540,SCROLLBARS=1,RESIZABLE=1,TOOLBAR=0,MENUBAR=0,STATUS=0,LOCATION=0,DIRECTORIES=0");'>
                    </td>
                <{/if}>
            </tr>
        </table>
    </form>

    <{if $image_total > 0}>
        <div id="pagenav"><{$pagenav}></div>
        <table cellspacing="0" id="imagemain">
            <tr>
                <th><{$lang_imagename}></th>
                <th><{$lang_image}></th>
                <th><{$lang_imagesize}></th>
                <th><{$lang_align}></th>
            </tr>

            <{foreach from=$photos item=photo}>
                <tr align="center">
                    <td>
                        <input type="hidden" name="photo_id[]" value="<{$photo.lid}>">
                        <{if $photo.can_edit}>
                            <a href='<{$mod_url}>/editphoto.php?lid=<{$photo.lid}>' target='_blank'><img
                                        src="<{xoModuleIcons16 'edit.png'}>"  border='0' alt=''></a>
                        <{/if}>
                        <{$photo.nicename}>
                    </td>
                    <td><img src="<{$photo.src}>" <{$photo.width_spec}> alt=""></td>
                    <td><{$photo.res_x}>x<{$photo.res_y}><br>(<{$photo.ext}>)</td>
                    <td nowrap="nowrap">

                        <{if $makethumb || ! $photo.is_normal }>
                            <a href="#" onclick="appendCode('<{$photo.xcodel}>');"><img
                                        src="<{$mod_url}>/assets/images/alignleft.gif" alt="<{$lang_left}>"
                                        title="<{$lang_left}>"></a>
                            <a href="#" onclick="appendCode('<{$photo.xcodec}>');"><img
                                        src="<{$mod_url}>/assets/images/aligncenter.gif" alt="<{$lang_center}>"
                                        title="<{$lang_center}>"></a>
                            <a href="#" onclick="appendCode('<{$photo.xcoder}>');"><img
                                        src="<{$mod_url}>/assets/images/alignright.gif" alt="<{$lang_right}>"
                                        title="<{$lang_right}>"></a>
                            <br>
                            <br>
                        <{/if}>

                        <{if $photo.is_normal}>
                            <a href="#" onclick="appendCode('<{$photo.xcodebl}>');"><img
                                        src="<{$mod_url}>/assets/images/alignbigleft.gif" alt="<{$lang_left}>"
                                        title="<{$lang_left}>"></a>
                            <a href="#" onclick="appendCode('<{$photo.xcodebc}>');"><img
                                        src="<{$mod_url}>/assets/images/alignbigcenter.gif"
                                        alt="<{$lang_center}>" title="<{$lang_center}>"></a>
                            <a href="#" onclick="appendCode('<{$photo.xcodebr}>');"><img
                                        src="<{$mod_url}>/assets/images/alignbigright.gif" alt="<{$lang_right}>"
                                        title="<{$lang_right}>"></a>
                        <{/if}>

                    </td>
                </tr>
            <{/foreach}>
        </table>
    <{/if}>

    <div id="pagenav"><{$pagenav}></div>

    <div id="footer">
        <input value="<{$lang_close}>" type="button" onclick="window.close();">
    </div>

    </body>
<{/strip}>
</html>
