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
function docleanup($data)
{
    global $INSTALLER09, $queries;
    set_time_limit(1200);
    ignore_user_abort(1);
    $sql = sql_query("SHOW PROCESSLIST");
    $cnt = 0;
    while ($arr = mysqli_fetch_assoc($sql)) {
        if ($arr['db'] == $INSTALLER09['mysql_db'] AND $arr['Command'] == 'Sleep' AND $arr['Time'] > 60) {
            sql_query("KILL {$arr['Id']}");
            $cnt++;
        }
    }
    if ($queries > 0) write_log("Proccess Kill clean-------------------- Proccess Kill Complete using $queries queries --------------------");
    if ($cnt != 0) {
        $data['clean_desc'] = "MySQLCleanup killed {$cnt} processes";
    }
    if ($data['clean_log']) {
        cleanup_log($data);
    }
}
?>
