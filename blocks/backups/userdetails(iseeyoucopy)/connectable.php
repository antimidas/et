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
//==Connectable and port shit
if ($user['paranoia'] < 1 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) {
        $What_Cache = (XBT_TRACKER == true ? 'port_data_xbt_' : 'port_data_' );
    if (($port_data = $mc1->get_value($What_Cache . $id)) === false) {
        if(XBT_TRACKER == true) {
        $q1 = sql_query('SELECT `connectable`, `peer_id` FROM `xbt_files_users` WHERE uid = ' . sqlesc($id) . ' LIMIT 1') or sqlerr(__FILE__, __LINE__);
        } else {
        $q1 = sql_query('SELECT connectable, port, agent FROM peers WHERE userid = ' . sqlesc($id) . ' LIMIT 1') or sqlerr(__FILE__, __LINE__);
        }
        $port_data = mysqli_fetch_row($q1);
        $mc1->cache_value('port_data_' . $id, $port_data, $INSTALLER09['expires']['port_data']);
    }
    if ($port_data > 0) {
        $connect = $port_data[0];
        $port = (XBT_TRACKER == true ? '' : $port_data[1]);
        $Ident_Client = (XBT_TRACKER == true ? $port_data['1'] : $port_data[2]);
        $OCELOT_or_PHP = (XBT_TRACKER == true ? '1' : 'yes');
        if ($connect == $OCELOT_or_PHP) {
            $connectable = "<font color='green'><i class='fa fa-check' aria-hidden='true'></i></font>";
        } else {
            $connectable = "<font color='red'><i class='fa fa-times' aria-hidden='true'></i></font>";
        }
    } else {
        $connectable = "<font color='orange'><i class='fa fa-question' aria-hidden='true'></i></b></font>";
    }
    $HTMLOUT.= "<tr>
		<td>{$lang['userdetails_connectable']}</td>
		<td>" . $connectable . "</td>
	</tr>";
    if (!empty($port))
		$HTMLOUT.= "<tr>
			<td>{$lang['userdetails_port']}</td>
			<td>" . htmlsafechars($port) . "</td>
		</tr>
		<tr>
			<td>{$lang['userdetails_client']}</td>
			<td>" . htmlsafechars($Ident_Client) . "</td>
		</tr>";
}
//==End
// End Class
// End File
