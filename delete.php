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
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'bittorrent.php');
require_once (INCL_DIR . 'user_functions.php');
require_once (INCL_DIR . 'function_memcache.php');
dbconn();
loggedinorreturn();
$lang = array_merge(load_language('global') , load_language('delete'));
if (!mkglobal("id")) stderr("{$lang['delete_failed']}", "{$lang['delete_missing_data']}");
$id = 0 + $id;
if (!is_valid_id($id)) stderr("{$lang['delete_failed']}", "{$lang['delete_missing_data']}");
//==delete torrents by putyn
function deletetorrent($id)
{
    global $INSTALLER09, $mc1, $CURUSER, $lang;
    sql_query("DELETE peers.*, files.*, comments.*, snatched.*, thanks.*, bookmarks.*, coins.*, rating.*, thumbsup.*, torrents.* FROM torrents 
				 LEFT JOIN peers ON peers.torrent = torrents.id
				 LEFT JOIN files ON files.torrent = torrents.id
				 LEFT JOIN comments ON comments.torrent = torrents.id
				 LEFT JOIN thanks ON thanks.torrentid = torrents.id
				 LEFT JOIN bookmarks ON bookmarks.torrentid = torrents.id
				 LEFT JOIN coins ON coins.torrentid = torrents.id
				 LEFT JOIN rating ON rating.torrent = torrents.id
                                 LEFT JOIN thumbsup ON thumbsup.torrentid = torrents.id
				 LEFT JOIN snatched ON snatched.torrentid = torrents.id
				 WHERE torrents.id =" . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
    unlink("{$INSTALLER09['torrent_dir']}/$id.torrent");
    $mc1->delete_value('MyPeers_' . $CURUSER['id']);
}
function deletetorrent_ocelot($id)
{
   global $INSTALLER09, $mc1, $CURUSER, $lang;
   $res = sql_query('SELECT info_hash FROM torrents WHERE id = ' . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
   $row = mysqli_fetch_assoc($res);
   sql_query("DELETE files.*, comments.*, thankyou.*, thanks.*, thumbsup.*, bookmarks.*, coins.*, rating.*, xbt_files_users.*, torrents.* FROM torrents
                                     LEFT JOIN files ON files.torrent = torrents.id
                                     LEFT JOIN comments ON comments.torrent = torrents.id
                                     LEFT JOIN thankyou ON thankyou.torid = torrents.id
                                     LEFT JOIN thanks ON thanks.torrentid = torrents.id
                                     LEFT JOIN bookmarks ON bookmarks.torrentid = torrents.id
                                     LEFT JOIN coins ON coins.torrentid = torrents.id
                                     LEFT JOIN rating ON rating.torrent = torrents.id
                                     LEFT JOIN thumbsup ON thumbsup.torrentid = torrents.id
                                     LEFT JOIN xbt_files_users ON xbt_files_users.fid = torrents.id
                                     WHERE torrents.id =" . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
        unlink("{$INSTALLER09['torrent_dir']}/$id.torrent");
        $mc1->delete_value('MyPeers_OCELOT_' . $CURUSER['id']);
        require_once(CLASS_DIR . 'tracker.class.php');
        Tracker::update_tracker('delete_torrent', array('info_hash' => rawurlencode($row['info_hash']), 'reason' => -1, 'id' => $id));
    }
$res = sql_query("SELECT name, owner, seeders FROM torrents WHERE id =" . sqlesc($id));
$row = mysqli_fetch_assoc($res);
if (!$row) stderr("{$lang['delete_failed']}", "{$lang['delete_not_exist']}");
if ($CURUSER["id"] != $row["owner"] && $CURUSER["class"] < UC_STAFF) stderr("{$lang['delete_failed']}", "{$lang['delete_not_owner']}\n");
$rt = 0 + $_POST["reasontype"];
if (!is_int($rt) || $rt < 1 || $rt > 5) stderr("{$lang['delete_failed']}", "{$lang['delete_invalid']}");
$reason = $_POST["reason"];
if ($rt == 1) $reasonstr = "{$lang['delete_dead']}";
elseif ($rt == 2) $reasonstr = "{$lang['delete_dupe']}" . ($reason[0] ? (": " . trim($reason[0])) : "!");
elseif ($rt == 3) $reasonstr = "{$lang['delete_nuked']}" . ($reason[1] ? (": " . trim($reason[1])) : "!");
elseif ($rt == 4) {
    if (!$reason[2]) stderr("{$lang['delete_failed']}", "{$lang['delete_violated']}");
    $reasonstr = $INSTALLER09['site_name'] . "{$lang['delete_rules']}" . trim($reason[2]);
} else {
    if (!$reason[3]) stderr("{$lang['delete_failed']}", "{$lang['delete_reason']}");
    $reasonstr = trim($reason[3]);
}
if (XBT_TRACKER == true) {
deletetorrent_ocelot($id);
} else {
deletetorrent($id);
remove_torrent_peers($id);
}
//$mc1->delete_value('lastest_tor_');
$mc1->delete_value('top5_tor_');
$mc1->delete_value('last5_tor_');
$mc1->delete_value('scroll_tor_');
$mc1->delete_value('torrent_details_' . $id);
$mc1->delete_value('torrent_details_text' . $id);
write_log("{$lang['delete_torrent']} $id ({$row['name']}){$lang['delete_deleted_by']}{$CURUSER['username']} ($reasonstr)\n");
if ($INSTALLER09['seedbonus_on'] == 1) {
    //===remove karma
    sql_query("UPDATE users SET seedbonus = seedbonus-".sqlesc($INSTALLER09['bonus_per_delete'])." WHERE id = " . sqlesc($row["owner"])) or sqlerr(__FILE__, __LINE__);
    $update['seedbonus'] = ($CURUSER['seedbonus'] - $INSTALLER09['bonus_per_delete']);
    $mc1->begin_transaction('userstats_' . $row["owner"]);
    $mc1->update_row(false, array(
        'seedbonus' => $update['seedbonus']
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
    $mc1->begin_transaction('user_stats_' . $row["owner"]);
    $mc1->update_row(false, array(
        'seedbonus' => $update['seedbonus']
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
    //===end
    
}
if ($CURUSER["id"] != $row["owner"] AND $CURUSER['pm_on_delete'] == 'yes') {
    $added = TIME_NOW;
    $pm_on = (int)$row["owner"];
    $message = "Torrent $id (" . htmlsafechars($row['name']) . ") has been deleted.\n  Reason: $reasonstr";
    sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, " . sqlesc($pm_on) . "," . sqlesc($message) . ", $added)") or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('inbox_new_' . $pm_on);
    $mc1->delete_value('inbox_new_sb_' . $pm_on);
}
if (isset($_POST["returnto"])) $ret = "<a href='" . htmlsafechars($_POST["returnto"]) . "'>{$lang['delete_go_back']}</a>";
else $ret = "<a href='{$INSTALLER09['baseurl']}/browse.php'>{$lang['delete_back_browse']}</a>";
$HTMLOUT = '';
$HTMLOUT.= "<h2>{$lang['delete_deleted']}</h2>
    <p>$ret</p>";
echo stdhead("{$lang['delete_deleted']}") . $HTMLOUT . stdfoot();
?>
