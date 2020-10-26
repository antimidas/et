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
//==Memcached message query
if ($INSTALLER09['msg_alert'] && $CURUSER) {
    if (($unread = $mc1->get_value('inbox_new_' . $CURUSER['id'])) === false) {
        $res = sql_query('SELECT count(id) FROM messages WHERE receiver=' . sqlesc($CURUSER['id']) . ' && unread="yes" AND location = "1"') or sqlerr(__FILE__, __LINE__);
        $arr = mysqli_fetch_row($res);
        $unread = (int)$arr[0];
        $mc1->cache_value('inbox_new_' . $CURUSER['id'], $unread, $INSTALLER09['expires']['unread']);
    }
}
/*
if (($CURUSER['pm_forced'] == 'yes') AND (!defined("INBOX_SCRIPT")) AND ($unread)) {
   header("Location: {$INSTALLER09['baseurl']}/pm_system.php");
   die;
}
//==End
*/
if ($INSTALLER09['msg_alert'] && isset($unread) && !empty($unread)) {
			$ress = sql_query('SELECT msgg.receiver, msgg.subject, msgg.sender, msgg.unread, msgg.msg, msgg.added, u1.username AS u1_username, u2.username AS u2_username FROM messages AS msgg LEFT JOIN users AS u1 ON u1.id=msgg.receiver LEFT JOIN users AS u2 ON u2.id=msgg.sender WHERE msgg.receiver=' . sqlesc($CURUSER['id']) . ' AND unread = "yes" AND msgg.location = 1 ORDER BY msgg.added DESC LIMIT 1') or sqlerr(__FILE__, __LINE__);
			$quickmessage = mysqli_fetch_assoc($ress);
			if (!$ress) stderr('Error', 'Something Wrong');
			if ($quickmessage["sender"] != 0)
			$sender = "<a href='userdetails.php?id=".(int)$quickmessage["sender"]."'><b>".htmlsafechars($quickmessage["u2_username"])."</b></a>";
			else $sender = "<font color='red'><b>System</b></font>";
			$htmlout .= '
	   <a class="small button" data-toggle="exampleModal6"><b>' . ($unread > 1 ? $lang['gl_newprivs'] . $lang['gl_newmesss'] : $lang['gl_newpriv'] . $lang['gl_newmess']) . '</b></a>
	<div class="reveal" id="exampleModal6" data-animation-in="spin-in" data-animation-out="spin-out" data-reveal>
      <p>Messages</p>
	  <div class="callout">
	<p><b>From</b>: ' . $sender . '</p>
	<p><b>Subject</b>: ' . ($quickmessage['subject'] !== '' ? htmlsafechars($quickmessage['subject']) : 'no subject' ) . '</p>
	<p><b>Message</b>:' . format_comment($quickmessage['msg']) . '</p>
	<p><b>Date</b>: ' . get_date($quickmessage['added'], '') . '</p>
	</div>';
			$htmlout.= '<a href="#!" class="modal-close" title="Close this modal" data-close="Close"
        data-dismiss="reveal">?</a>
      <button class="close-button close" data-dismiss="reveal" aria-label="Close reveal" type="button">
        <span class="close" aria-hidden="true">&times;</span>
      </button>
    </div>';
}
//==
// End Class
// End File
