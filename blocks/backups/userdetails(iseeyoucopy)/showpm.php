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
     _   _   _   _     _   _   _   _   _   _   _ 
 / \ / \ / \ / \   / \ / \ / \ / \ / \ / \ / \
| E | v | i | l )-| T | r | i | n | i | t | y )
 \_/ \_/ \_/ \_/   \_/ \_/ \_/ \_/ \_/ \_/ \_/

*/
if ($CURUSER["id"] != $user["id"]) if ($CURUSER['class'] >= UC_STAFF) $showpmbutton = 1;
elseif ($user["acceptpms"] == "yes") {
    $r = sql_query("SELECT id FROM blocks WHERE userid=" . sqlesc($user['id']) . " AND blockid=" . sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
    $showpmbutton = (mysqli_num_rows($r) == 1 ? 0 : 1);
} elseif ($user["acceptpms"] == "friends") {
    $r = sql_query("SELECT id FROM friends WHERE userid=" . sqlesc($user['id']) . " AND friendid=" . sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
    $showpmbutton = (mysqli_num_rows($r) == 1 ? 1 : 0);
}
if (isset($showpmbutton)) 
	$HTMLOUT.= "<div class='card-section large-2 columns'>
      <form method='get' action='pm_system.php?'>
        <input type='hidden' name='action' value='send_message'>
        <input type='hidden' name='receiver' value='" . (int)$user["id"] . "'>
        <input type='hidden' name='returnto' value='" . urlencode($_SERVER['REQUEST_URI']) . "'>
        <input type='submit' value='{$lang['userdetails_msg_btn']}' class='button'>
      </form>
	  </div>";
//==end
// End Class
// End File
