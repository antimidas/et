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
//==Uploaded/downloaded
if ($user['paranoia'] < 2 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) {
    $days = round((TIME_NOW - $user['added']) / 86400);
    if ($INSTALLER09['ratio_free']) {
        $HTMLOUT.= "<tr><td>{$lang['userdetails_h_days']}</td>
			<td>{$lang['userdetails_rfree_effect']}</td></tr>
    <tr><td>{$lang['userdetails_uploaded']}</td><td>" . mksize($user_stats['uploaded']) . " {$lang['userdetails_daily']}" . ($days > 1 ? mksize($user_stats['uploaded'] / $days) : mksize($user_stats['uploaded'])) . "</td></tr>\n";
    } else {
        $HTMLOUT.= "<tr><td><strong>{$lang['userdetails_downloaded']}</strong></td><td align='left'>" . mksize($user_stats['downloaded']) . " {$lang['userdetails_daily']}" . ($days > 1 ? mksize($user_stats['downloaded'] / $days) : mksize($user_stats['downloaded'])) . "</td></tr>
    <tr><td><strong>{$lang['userdetails_uploaded']}</strong></td><td align='left'>" . mksize($user_stats['uploaded']) . " {$lang['userdetails_daily']}" . ($days > 1 ? mksize($user_stats['uploaded'] / $days) : mksize($user_stats['uploaded'])) . "</td></tr>\n";
    }
}
//==end
// End Class
// End File