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
if ($user["info"]) $HTMLOUT.= "<tr valign='top'><td align='left' colspan='2' class='text' bgcolor='#F4F4F0'>" . format_comment($user["info"]) . "</td></tr>\n";
if ($user["signature"]) $HTMLOUT.= "<tr valign='top'><td align='left' colspan='2' class='text' bgcolor='#F4F4F0'>" . format_comment($user["signature"]) . "</td></tr>\n";
//==end
// End Class
// End File
