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
require_once ROOT_DIR . 'tfreak.php';
$HTMLOUT.= "
<div class='header panel panel-default'>
	<div class='panel-heading'>
		<a class='accordion-toggle' data-toggle='collapse' data-parent='#accordion' href='#collapseTen'>
		<label for='checkbox_4' class='text-left'>'{$INSTALLER09['site_name']}{$lang['index_torr_freak']}</label>
		</a>
	</div>
	<div class='container-fluid panel-body'>";
			$HTMLOUT.= rsstfreakinfo();
			$HTMLOUT.= "
			</div></div>";
//==
// End Class
// End File
