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
    global $INSTALLER09, $queries, $mc1;
    set_time_limit(1200);
    ignore_user_abort(1);
    //== Delete expired announcements and processors
    sql_query("DELETE announcement_process FROM announcement_process LEFT JOIN users ON announcement_process.user_id = users.id WHERE users.id IS NULL") or sqlerr(__FILE__, __LINE__);
    sql_query("DELETE FROM announcement_main WHERE expires < " . TIME_NOW) or sqlerr(__FILE__, __LINE__);
    sql_query("DELETE announcement_process FROM announcement_process LEFT JOIN announcement_main ON announcement_process.main_id = announcement_main.main_id WHERE announcement_main.main_id IS NULL") or sqlerr(__FILE__, __LINE__);
    if ($queries > 0) write_log("Announcement Clean -------------------- Announcement cleanup Complete using $queries queries --------------------");
    if (false !== mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        $data['clean_desc'] = mysqli_affected_rows($GLOBALS["___mysqli_ston"]) . " items deleted/updated";
    }
    if ($data['clean_log']) {
        cleanup_log($data);
    }
}
?>
