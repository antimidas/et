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
// Announcement Code...
$ann_subject = trim($CURUSER['curr_ann_subject']);
$ann_body = trim($CURUSER['curr_ann_body']);
if ((!empty($ann_subject)) AND (!empty($ann_body)))
   {
   $HTMLOUT .= "
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<label for='checkbox_4' class='text-left'>{$lang['index_announce']}</label>
		</div>
		<div class='panel-body'>
			<table class='table table-bordered'>
				<tr>
					<td bgcolor='transparent'><b><font color='red'>{$lang['index_ann_title']}&nbsp;: " . htmlsafechars($ann_subject) . "</font></b></td>
				</tr>
				<tr>
					<td style='padding: 10px; background:lightgrey'>" . format_comment($ann_body) . "<br /><hr /><br />   {$lang['index_ann_click']}<a href='{$INSTALLER09['baseurl']}/clear_announcement.php'>
					<i><b>{$lang['index_ann_here']}</b></i></a>{$lang['index_ann_clear']}</td>
				</tr>
			</table>
		</div>
		<br />\n";
   }
//==End
// End Class
// End File
