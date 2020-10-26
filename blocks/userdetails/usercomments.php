<?php
/**
 |--------------------------------------------------------------------------|
 |   https://github.com/3evils/                                             |
 |--------------------------------------------------------------------------|
 |   Licence Info: WTFPL                                                    |
 |--------------------------------------------------------------------------|
 |   Copyright (C) 2020 Evil-Trinity                                        |
 |--------------------------------------------------------------------------|
 |   A bittorrent tracker source based on an unreleased U-232               |
 |--------------------------------------------------------------------------|
 |   Project Leaders: AntiMidas,  Seeder                                    |
 |--------------------------------------------------------------------------|
 |   All other snippets, mods and contributions for this version from:      |
 | CoLdFuSiOn, *putyn, pdq, djGrrr, Retro, elephant, ezero, Alex2005,       |
 | system, sir_Snugglebunny, laffin, Wilba, Traffic, dokty, djlee, neptune, |
 | scars, Raw, soft, jaits, Melvinmeow, RogueSurfer, stoner, Stillapunk,    |
 | swizzles, autotron, stonebreath, whocares, Tundracanine , son            |
 |                                                                                                                            |
 |--------------------------------------------------------------------------|
                 _   _   _   _     _   _   _   _   _   _   _
                / \ / \ / \ / \   / \ / \ / \ / \ / \ / \ / \
               | E | v | i | l )-| T | r | i | n | i | t | y )
                \_/ \_/ \_/ \_/   \_/ \_/ \_/ \_/ \_/ \_/ \_/
*/
//==Usercomments - pdq
$HTMLOUT.= "<h1>{$lang['userdetails_comm_left']}<a href='userdetails.php?id=$id'>" . htmlsafechars($user['username']) . "</a></h1>
    <a name='startcomments'></a>";
$commentbar = "<a href='usercomment.php?action=add&amp;userid={$id}'>Add a comment</a>\n";
$subres = sql_query("SELECT COUNT(id) FROM usercomments WHERE userid = " . sqlesc($id));
$subrow = mysqli_fetch_array($subres, MYSQLI_NUM);
$count = $subrow[0];
if (!$count) {
    $HTMLOUT.= "<h2>{$lang['userdetails_comm_yet']}</h2>\n";
} else {
    require_once (INCL_DIR . 'pager_functions.php');
    $pager = pager(5, $count, "userdetails.php?id=$id&amp;", array(
        'lastpagedefault' => 1
    ));
    $subres = sql_query("SELECT usercomments.id, usercomments.text, usercomments.user, usercomments.added, usercomments.editedby, usercomments.editedat, usercomments.edit_name, usercomments.user_likes, users.avatar, users.warned, users.username, users.title, users.class, users.leechwarn, users.chatpost, users.pirate, users.king, users.donor FROM usercomments LEFT JOIN users ON usercomments.user = users.id WHERE userid = ".sqlesc($id)." ORDER BY usercomments.id {$pager['limit']}") or sqlerr(__FILE__, __LINE__);
    $allrows = array();
    while ($subrow = mysqli_fetch_assoc($subres)) $allrows[] = $subrow;
    $HTMLOUT.= ($commentbar);
    $HTMLOUT.= ($pager['pagertop']);
    $HTMLOUT.= usercommenttable($allrows);
    $HTMLOUT.= ($pager['pagerbottom']);
}
$HTMLOUT.= ($commentbar);
//==end
// End Class
// End File
