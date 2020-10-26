<?php
/**
 |--------------------------------------------------------------------------|
 |   https://github.com/Bigjoos/                                            |
 |--------------------------------------------------------------------------|
 |   Licence Info: WTFPL                                                    |
 |--------------------------------------------------------------------------|
 |   Copyright (C) 2018 U-232 CodeName Trinity                              |
 |--------------------------------------------------------------------------|
 |   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.   |
 |--------------------------------------------------------------------------|
 |   Project Leaders: Mindless, Autotron, whocares, Swizzles.               |
 |--------------------------------------------------------------------------|
      _   _   _   _     _   _   _   _   _   _   _ 
 / \ / \ / \ / \   / \ / \ / \ / \ / \ / \ / \
| E | v | i | l )-| T | r | i | n | i | t | y )
 \_/ \_/ \_/ \_/   \_/ \_/ \_/ \_/ \_/ \_/ \_/

*/

include_once('./cache/api_keys.php');
include('./include/class/class_details.php');
$stdhead = array(
    /** include css **/
    'css' => array(
	'bbcode',
        'details',
        'rating_style'
    )
);
$stdfoot = array(
    /** include js **/
    'js' => array(
        'popup',
        'jquery.thanks',
        'wz_tooltip',
        'java_klappe',
        'balloontip',
        'shout',
        'thumbs',
        'sack'
    )
);
$HTMLOUT = $torrent_cache = '';
if (!isset($_GET['id']) || !is_valid_id($_GET['id'])) stderr("{$lang['details_user_error']}", "{$lang['details_bad_id']}");
$id = (int)$_GET["id"];

$slot = make_freeslots($CURUSER['id'], 'fllslot_');
$torrent['addedfree'] = $torrent['addedup'] = $free_slot = $double_slot = '';
if (!empty($slot)) foreach ($slot as $sl) {
    if ($sl['torrentid'] == $id && $sl['free'] == 'yes') {
        $free_slot = 1;
        $torrent['addedfree'] = $sl['addedfree'];
    }
    if ($sl['torrentid'] == $id && $sl['doubleup'] == 'yes') {
        $double_slot = 1;
        $torrent['addedup'] = $sl['addedup'];
    }
    if ($free_slot && $double_slot) break;
}
$categorie = genrelist();
foreach ($categorie as $key => $value) $change[$value['id']] = array(
    'id' => $value['id'],
    'name' => $value['name'],
    'image' => $value['image'],
    'min_class' => $value['min_class']
);
if (($torrents = $mc1->get_value('torrent_details_' . $id)) === false) {
    $tor_fields_ar_int = array(
        'id',
        'leechers',
        'seeders',
        'thanks',
        'comments',
        'owner',
        'size',
        'added',
        'views',
        'hits',
        'numfiles',
        'times_completed',
        'points',
        'last_reseed',
        'category',
        'free',
        'freetorrent',
        'silver',
        'rating_sum',
        'checked_when',
        'num_ratings',
        'mtime',
        'checked_when',
        'f_points'
    );
    $tor_fields_ar_str = array(
        'banned',
        'info_hash',
        'checked_by',
        'filename',
        'search_text',
        'name',
        'save_as',
        'visible',
        'type',
        'poster',
        'url',
        'anonymous',
        'allow_comments',
        'description',
        'nuked',
        'nukereason',
        'vip',
        'subs',
        'username',
        'newgenre',
        'release_group',
        'youtube',
        'tags',
        'user_likes'
    );
    $tor_fields = implode(', ', array_merge($tor_fields_ar_int, $tor_fields_ar_str));
    $result = sql_query("SELECT " . $tor_fields . ", (SELECT MAX(id) FROM torrents ) as max_id, (SELECT MIN(id) FROM torrents) as min_id, LENGTH(nfo) AS nfosz, IF(num_ratings < {$INSTALLER09['minvotes']}, NULL, ROUND(rating_sum / num_ratings, 1)) AS rating FROM torrents WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
    $torrents = mysqli_fetch_assoc($result);
    foreach ($tor_fields_ar_int as $i) $torrents[$i] = (int)$torrents[$i];
    foreach ($tor_fields_ar_str as $i) $torrents[$i] = $torrents[$i];
    $mc1->cache_value('torrent_details_' . $id, $torrents, $INSTALLER09['expires']['torrent_details']);
}



$ytrailers = $torrents["youtube"];


$HTMLOUT.= "<script type='text/javascript'>
    /*<![CDATA[*/
	//var e = new sack();
function do_rate(rate,id,what) {
		var box = document.getElementById('rate_'+id);
		e.setVar('rate',rate);
		e.setVar('id',id);
		e.setVar('ajax','1');
		e.setVar('what',what);
		e.requestFile = 'rating.php';
		e.method = 'GET';
		e.element = 'rate_'+id;
		e.onloading = function () {
			box.innerHTML = 'Loading ...'
		}
		e.onCompletion = function() {
			if(e.responseStatus)
				box.innerHTML = e.response();
		}
		e.onerror = function () {
			alert('That was something wrong with the request!');
		}
		e.runAJAX();
}
/*]]>*/
</script>";

if (isset($_GET["uploaded"])) {
    $HTMLOUT.= "<div data-closable class='alert-box callout success text-center'><h6><i class='fa fa-check'></i>{$lang['details_success']}</h6><button class='close-button' aria-label='Dismiss alert' type='button' data-close>
    <span aria-hidden='true'>&CircleTimes;</span>
  </button>";
    $HTMLOUT.= "<p>{$lang['details_start_seeding']}</p></div>\n";
    $HTMLOUT.= '<meta http-equiv="refresh" content="1;url=download.php?torrent=' . $id . '' . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . '" />';
} elseif (isset($_GET["edited"])) {
    $HTMLOUT.= "<div data-closable class='alert-box callout success text-center'><h6><i class='fa fa-check'></i>{$lang['details_success_edit']}</h6>
	<button class='close-button' aria-label='Dismiss alert' type='button' data-close>
    <span aria-hidden='true'>&CircleTimes;</span>
  </button></div>";
    if (isset($_GET["returnto"])) $HTMLOUT.= "<p><b>{$lang['details_go_back']}<a href='" . htmlsafechars($_GET["returnto"]) . "'>{$lang['details_whence']}</a>.</b></p>\n";
} elseif (isset($_GET["reseed"])) {
    $HTMLOUT.= "<div data-closable class='alert-box callout success text-center'>
		<h6>
			<i class='fa fa-check'></i>PM was sent! Now wait for a seeder !
		</h6>
		<button class='close-button' aria-label='Dismiss alert' type='button' data-close>
			<span aria-hidden='true'>&CircleTimes;</span>
		</button>
	</div>";
}
if ($CURUSER['class'] >= UC_STAFF) {
    if (isset($_GET["checked"]) && $_GET["checked"] == 1) {
        sql_query("UPDATE torrents SET checked_by = " . sqlesc($CURUSER['username']) . ",checked_when = ".TIME_NOW." WHERE id =" . sqlesc($id) . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $mc1->begin_transaction('torrent_details_' . $id);
        $mc1->update_row(false, array(
            'checked_by' => $CURUSER['username'],
            'checked_when' => TIME_NOW
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['torrent_details']);
        $mc1->delete_value('checked_by_' . $id);
        write_log("Torrent <a href={$INSTALLER09['baseurl']}/details.php?id=$id>(" . htmlsafechars($torrents['name']) . ")</a> was checked by {$CURUSER['username']}");
        header("Location: {$INSTALLER09["baseurl"]}/details.php?id=$id&checked=done#Success");
    } elseif (isset($_GET["rechecked"]) && $_GET["rechecked"] == 1) {
        sql_query("UPDATE torrents SET checked_by = " . sqlesc($CURUSER['username']) . ",checked_when = ".TIME_NOW." WHERE id =" . sqlesc($id) . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $mc1->begin_transaction('torrent_details_' . $id);
        $mc1->update_row(false, array(
            'checked_by' => $CURUSER['username'],
            'checked_when' => TIME_NOW
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['torrent_details']);
        $mc1->delete_value('checked_by_' . $id);
        write_log("Torrent <a href={$INSTALLER09['baseurl']}/details.php?id=$id>(" . htmlsafechars($torrents['name']) . ")</a> was re-checked by {$CURUSER['username']}");
        header("Location: {$INSTALLER09["baseurl"]}/details.php?id=$id&rechecked=done#Success");
    } elseif (isset($_GET["clearchecked"]) && $_GET["clearchecked"] == 1) {
        sql_query("UPDATE torrents SET checked_by = '', checked_when='' WHERE id =" . sqlesc($id) . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $mc1->begin_transaction('torrent_details_' . $id);
        $mc1->update_row(false, array(
            'checked_by' => '',
            'checked_when' => ''
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['torrent_details']);
        $mc1->delete_value('checked_by_' . $id);
        write_log("Torrent <a href={$INSTALLER09["baseurl"]}/details.php?id=$id>(" . htmlsafechars($torrents['name']) . ")</a> was un-checked by {$CURUSER['username']}");
        header("Location: {$INSTALLER09["baseurl"]}/details.php?id=$id&clearchecked=done#Success");
    }


    if (isset($_GET["checked"]) && $_GET["checked"] == 'done') $HTMLOUT.= "<div class='alert alert-success span11' align='center'><h2><a name='Success'>Successfully checked {$CURUSER['username']}!</a></h2></div>";
    if (isset($_GET["rechecked"]) && $_GET["rechecked"] == 'done') $HTMLOUT.= "<div class='alert alert-success span11' align='center'><h2><a name='Success'>Successfully re-checked {$CURUSER['username']}!</a></h2></div>";
    if (isset($_GET["clearchecked"]) && $_GET["clearchecked"] == 'done') $HTMLOUT.= "<div class='alert alert-success span11' align='center'><h2><a name='Success'>Successfully un-checked {$CURUSER['username']}!</a></h2></div>";
}

if (isset($_GET["returnto"])) {
    $addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
    $url.= $addthis;
    $keepget = $addthis;
}



$editlink = "a href=\"$url\" class=\"sublink\"";

// end
/** free mod pdq **/
$HTMLOUT.= '<div id="balloon1" class="balloonstyle">
			Once chosen this torrent will be Freeleech ' . $torrent['freeimg'] . ' until ' . get_date($torrent['idk'], 'DATE') . ' and can be resumed or started over using the
			regular download link. Doing so will result in one Freeleech Slot being taken away from your total.
		</div>
        <div id="balloon2" class="balloonstyle">
			Once chosen this torrent will be Doubleseed ' . $torrent['doubleimg'] . ' until ' . get_date($torrent['idk'], 'DATE') . ' and can be resumed or started over using the
			regular download link. Doing so will result in one Freeleech Slot being taken away from your total.
		</div>
		<div id="balloon3" class="balloonstyle">
			Remember to show your gratitude and Thank the Uploader. <img src="' . $INSTALLER09['pic_base_url'] . 'smilies/smile1.gif" alt="" />
		</div>';
/** end **/
if (!($CURUSER["downloadpos"] == 0 && $CURUSER["id"] != $torrents["owner"] OR $CURUSER["downloadpos"] > 1)) {
    /** free mod by pdq **/
    //== Display the freeslots links etc.
    if ($free_slot && !$double_slot) {
        $HTMLOUT.= '<tr>
        <td align="right" class="heading">'.$lang['details_add_slots1'].'</td>
        <td align="left">' . $torrent['freeimg'] . ' <b><font color="' . $torrent['free_color'] . '">'.$lang['details_add_slots2'].'</font></b>'.$lang['details_add_slots3'].'' . $torrent['addfree'] . '</td></tr>';
        $freeslot = ((!OCELOT_TRACKER && $CURUSER['freeslots'] >= 1) ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=double\" rel='balloon2' onclick=\"return confirm('".$lang['details_add_slots5']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6']}</b></font></a>&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
        $freeslot_zip = ((!OCELOT_TRACKER && $CURUSER['freeslots'] >= 1) ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=double&amp;zip=1\" rel='balloon2' onclick=\"return confirm('".$lang['details_add_slots5']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6']}</b></font></a>&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
        $freeslot_text = ((!OCELOT_TRACKER && $CURUSER['freeslots'] >= 1) ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=double&amp;text=1\" rel='balloon2' onclick=\"return confirm('".$lang['details_add_slots5']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6']}</b></font></a>&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
    } elseif (!$free_slot && $double_slot) {
        $HTMLOUT.= '<tr>
        <td align="right" class="heading">'.$lang['details_add_slots1'].'</td>
        <td align="left">' . $torrent['doubleimg'] . ' <b><font color="' . $torrent['free_color'] . '">'.$lang['details_add_slots8'].'</font></b>'.$lang['details_add_slots3'].'' . $torrent['addup'] . '</td></tr>';
        $freeslot = ($CURUSER['freeslots'] >= 1 ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=free\" rel='balloon1' onclick=\"return confirm('".$lang['details_add_slots5f']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6f']}</b></font></a>&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
        $freeslot_zip = ($CURUSER['freeslots'] >= 1 ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=free&amp;zip=1\" rel='balloon1' onclick=\"return confirm('".$lang['details_add_slots5f']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6f']}</b></font></a>&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
        $freeslot_text = ($CURUSER['freeslots'] >= 1 ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=free&amp;text=1\" rel='balloon1' onclick=\"return confirm('".$lang['details_add_slots5f']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6f']}</b></font></a>&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
    } elseif ($free_slot && $double_slot) {
        $HTMLOUT.= '<tr>
        <td align="right" class="heading">'.$lang['details_add_slots1'].'</td>
        <td align="left">' . $torrent['freeimg'] . ' ' . $torrent['doubleimg'] . ' <b><font color="' . $torrent['free_color'] . '">'.$lang['details_add_slots9'].'</font></b>'.$lang['details_add_slots10'].'' . $torrent['addfree'] . ''.$lang['details_add_slots11'].'' . $torrent['addup'] . '</p></td></tr>';
        $freeslot = $freeslot_zip = $freeslot_text = '';
    } else {
    $freeslot = ($CURUSER['freeslots'] >= 1 ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=free\" rel='balloon1' onclick=\"return confirm('".$lang['details_add_slots5f']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6f']}</b></font></a>" . (!OCELOT_TRACKER ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=double\" rel='balloon2' onclick=\"return confirm('".$lang['details_add_slots5']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6']}</b></font></a>" : "" ) . "&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
        $freeslot_zip = ($CURUSER['freeslots'] >= 1 ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=free&amp;zip=1\" rel='balloon1' onclick=\"return confirm('".$lang['details_add_slots5f']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6f']}</b></font></a>" . (!OCELOT_TRACKER ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=double&amp;zip=1\" rel='balloon2' onclick=\"return confirm('".$lang['details_add_slots5']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6']}</b></font></a>" : "" ) . "&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
        $freeslot_text = ($CURUSER['freeslots'] >= 1 ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=free&amp;text=1\" rel='balloon1' onclick=\"return confirm('".$lang['details_add_slots5f']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6f']}</b></font></a>" . (!OCELOT_TRACKER ? "&nbsp;&nbsp;<b>{$lang['details_add_slots4']}</b><a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;slot=double&amp;text=1\" rel='balloon2' onclick=\"return confirm('".$lang['details_add_slots5']."')\"><font color='" . $torrent['free_color'] . "'><b>{$lang['details_add_slots6']}</b></font></a>" : "") . "&nbsp;- " . htmlsafechars($CURUSER['freeslots']) . "".$lang['details_add_slots7']."" : "");
    }
    //==
    require_once MODS_DIR . 'free_details.php';
$HTMLOUT .="<div class='row callout'>";
$prev_id = ($id - 1);
         $next_id = ($id + 1);
$s = htmlsafechars($torrents["name"], ENT_QUOTES);
$HTMLOUT.= "<div class='row'><h4 class='subheader'>$s</h4>";
if($torrents["id"] != $torrents["min_id"])
        $HTMLOUT .= "<a class='button float-left' href='details.php?id={$prev_id}'><b>[Prev Torrent]</b></a>";
        $HTMLOUT .= "<a class='button float-left' href='browse.php'><b>  [Return]</b></a>";
        if($torrents["id"] != $torrents["max_id"])
        $HTMLOUT .= "<a class='button float-right' href='details.php?id={$next_id}'><b>  [Next Torrent]</b></a>";
		$HTMLOUT.= "<a class='button float-right' href='random.php'>" . (!isset($_GET['random']) ? '[Random Any]' : '<span style="color:#3366FF;">[Random Any]</span>') . "</a>";
//Thumbs Up
if (($thumbs = $mc1->get_value('thumbs_up_' . $id)) === false) {
    $thumbs = mysqli_num_rows(sql_query("SELECT id, type, torrentid, userid FROM thumbsup WHERE torrentid = " . sqlesc($torrents['id'])));
    $thumbs = (int)$thumbs;
    $mc1->add_value('thumbs_up_' . $id, $thumbs, 0);
}
$HTMLOUT.= "<a class='button button-badge float-right' href=\"javascript:ThumbsUp('" . (int)$torrents['id'] . "')\">
	<i class='far fa-thumbs-up'></i><span class='badgeb badge-b alert'>" . $thumbs . "</span></a>";
//==
$HTMLOUT .="</div>";

$HTMLOUT.="<br>  
	<div class='row'>
	<div class='large-100 columns'>";
    $poster_url = ((empty($torrents['poster'])) ? $INSTALLER09['pic_base_url'] .'noposter.png' : htmlsafechars($torrents["poster"]));
    $HTMLOUT .= " <!-- closing details poster -->";
//==09 Poster mod
$HTMLOUT .= "<div style='display:block;height:20px'></div>";
    $Free_Slot = $freeslot;
    $Free_Slot_Zip = $freeslot_zip;
    $Free_Slot_Text = $freeslot_text;


    if (!empty($torrents["description"])) {
$HTMLOUT.= "<table class='table'><th class='text-center'>{$lang['details_small_descr']}</th><tr><td class='details-text-ellipsis'><i>" . htmlsafechars($torrents['description']) . "</i></td></tr></table>";
}
    if($torrents['description']) {
$HTMLOUT.= "<table class='table'><th class='text-center'>{$lang['details_small_descr']}</th><tr><td><i>No small description found</i></td></tr></table>";
}



$HTMLOUT.= "</div>";
$HTMLOUT.= "
<div style='margin-right:auto;margin-left:auto;width:95%;display:flex;'>
<div class='stacked' style='width:30%;display: flex'><img style='padding:25px;height:auto;width:100%;' src='$poster_url'></div>
	<div style='width: 95%;'><table align='center' class='table table-bordered span3'>\n";
//==subs by putyn
if (in_array($torrents["category"], $INSTALLER09['movie_cats']) && !empty($torrents["subs"])) {
    $HTMLOUT.= "<tr>
		<td class='rowhead'>Subtitles</td>
		<td align='left'>";
    $subs_array = explode(",", $torrents["subs"]);
    foreach ($subs_array as $k => $sid) {
        require_once (CACHE_DIR . 'subs.php');
        foreach ($subs as $sub) {
            if ($sub["id"] == $sid) $HTMLOUT.= "<img border=\"0\" width=\"25px\" style=\"padding:3px;\"src=\"" . htmlsafechars($sub["pic"]) . "\" alt=\"" . htmlsafechars($sub["name"]) . "\" title=\"" . htmlsafechars($sub["name"]) . "\" />";
        }
    }
    $HTMLOUT.= "</td></tr>\n";
}

/*$HTMLOUT.= tr("Rating", getRate($id, "torrent") , 1);
// --------------- likes start------
        $att_str = '';
        if (!empty($torrents['user_likes'])) {
            $likes = explode(',', $torrents['user_likes']);
        } else {
            $likes = '';
        }
        if (!empty($likes) && count(array_unique($likes)) > 0) {
            if (in_array($CURUSER['id'], $likes)) {
                if (count($likes) == 1) {
                    $att_str = jq('You like this');
                } elseif (count(array_unique($likes)) > 1) {
                    $att_str = jq('You and&nbsp;') . ((count(array_unique($likes)) - 1) == '1' ? '1 other person likes this' : (count($likes) - 1) . '&nbsp;others like this');
                }
            } elseif (!(in_array($CURUSER['id'], $likes))) {
                if (count(array_unique($likes)) == 1) {
                    $att_str = '1 other person likes this';
                } elseif (count(array_unique($likes)) > 1) {
                    $att_str = (count(array_unique($likes))) . '&nbsp;others like this';
                }
            }
        }
        $wht = ((!empty($likes) && count(array_unique($likes)) > 0 && in_array($CURUSER['id'], $likes)) ? 'unlike' : 'like');

$HTMLOUT.= tr("Likes","<span id='mlike' data-com='" . (int)$torrents["id"] . "' class='details {$wht}'>[" . ucfirst($wht) . "]</span><span class='tot-" . (int)$torrents["id"] . "' data-tot='" . (!empty($likes) && count(array_unique($likes)) > 0 ? count(array_unique($likes)) : '') . "'>&nbsp;{$att_str}</span>" , 1);
$HTMLOUT.= tr("{$lang['details_last_seeder']}", "{$lang['details_last_activity']}" . get_date($l_a['lastseed'], '', 0, 1));
$HTMLOUT.= tr("{$lang['details_size']}", mksize($torrents["size"]) . " (" . number_format($torrents["size"]) . " {$lang['details_bytes']})");
$HTMLOUT.= tr("{$lang['details_added']}", get_date($torrents['added'], "{$lang['details_long']}"));
//Display pretime
    if ($pretime['time'] == '0') {
    $prestatement = "No pretime found.";
    } else {
    $prestatement = get_pretime(time() -  $pretime['time']) . " ago<br />Uploaded " . get_pretime($torrents['added'] - $pretime['time']) . " after pre.";
    }
$HTMLOUT.="<tr><td align='right' class='heading'>Pre Time</td><td width='99%' align='left'>". $prestatement."</td></tr>";
$HTMLOUT.= tr("{$lang['details_views']}", (int)$torrents["views"]);
$HTMLOUT.= tr("{$lang['details_hits']}", (int)$torrents["hits"]);
$Ocelot_Or_Default = (OCELOT_TRACKER == true ? 'snatches_ocelot.php?id=' : 'snatches.php?id=');
$HTMLOUT.= tr("{$lang['details_snatched']}", ($torrents["times_completed"] > 0 ? "<a href='{$INSTALLER09["baseurl"]}/{$Ocelot_Or_Default}{$id}'>{$torrents['times_completed']} {$lang['details_times']}</a>" : "0 {$lang['details_times']}") , 1);*/
$HTMLOUT.= "
<script type='text/javascript'>
function showme() {
    document.getElementById('show').innerHTML = '{$CURUSER['username']} is viewing details for torrent {$INSTALLER09['baseurl']}/details.php?id=" . (int)$torrents['id'] . "\"';
}
</script>
<tr><td class='rowhead'>Status update</td><td><button type='button' class='small button'  id='show' onclick='showme()'>DO IT!</button></td></tr>
	<div>
	<table style='display:inline;' class='table'>
			<tr>
			<td align=\"right\" class=\"heading\" width=\"3%\">{$lang['details_download']}</td>
			<td align=\"left\" class='details-text-ellipsis'>
			<a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "\">&nbsp;<u>" . htmlsafechars($torrents["filename"]) . "</u></a>{$Free_Slot}
			</td>
			</tr>
			
			";
    /** end **/
    //==Torrent as zip by putyn
    $HTMLOUT.= "<tr>
		<td>{$lang['details_zip']}</td>
		<td align=\"left\" class='details-text-ellipsis'>
		<a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;zip=1\">&nbsp;<u>" . htmlsafechars($torrents["filename"]) . "</u></a>{$Free_Slot_Zip}</td></tr>";
    //==Torrent as text by putyn
    $HTMLOUT.= "<tr>
		<td>{$lang['details_text']}</td>
		<td align=\"left\" class='details-text-ellipsis'>
		<a class=\"index\" href=\"download.php?torrent={$id}" . ($CURUSER['ssluse'] == 3 ? "&amp;ssl=1" : "") . "&amp;text=1\">&nbsp;<u>" . htmlsafechars($torrents["filename"]) . "</u></a>{$Free_Slot_Text}</td></tr></table>";

function hex_esc($matches) {
	return sprintf("%02x", ord($matches[0]));
}
$HTMLOUT .= tr("{$lang['details_info_hash']}", '<div class="details-text-ellipsis">' .preg_replace_callback('/./s', "hex_esc", hash_pad($torrents["info_hash"])) . '</div></div>',true);
    $HTMLOUT.= "</table>\n</div></div>";
} else {
    $HTMLOUT.="<div><div class='container-fluid'><table class='table'><tr><td align='right' class='heading'>Download Disabled!!</td><td>Your not allowed to download presently !!</td></tr></table></div></div>";
}
if ($CURUSER["class"] >= UC_POWER_USER && $torrents["nfosz"] > 0) $HTMLOUT.= "<tr><br><td class='rowhead'>{$lang['details_nfo']}</td><br><td align='left'><a href='viewnfo.php?id=" . (int)$torrents['id'] . "'><b>{$lang['details_view_nfo']}</b></a> (" . mksize($torrents["nfosz"]) . ")</td></tr>\n";
if ($torrents["visible"] == "no") $HTMLOUT.= tr("{$lang['details_visible']}", "<b>{$lang['details_no']}</b>{$lang['details_dead']}", 1);
if ($moderator) $HTMLOUT.= tr("{$lang['details_banned']}", $torrents["banned"]);
if ($torrents["nuked"] == "yes") $HTMLOUT.= "<tr><td class='rowhead'><b>Nuked</b></td><td align='left'><img src='{$INSTALLER09['pic_base_url']}nuked.gif' alt='Nuked' title='Nuked' /></td></tr>\n";
if (!empty($torrents["nukereason"])) $HTMLOUT.= "<tr><td class='rowhead'><b>Nuke-Reason</b></td><td align='left'>" . htmlsafechars($torrents["nukereason"]) . "</td></tr>\n";
$torrents['cat_name'] = htmlsafechars($change[$torrents['category']]['name']);
if (isset($torrents["cat_name"])) $HTMLOUT.= tr("{$lang['details_type']}", htmlsafechars($torrents["cat_name"]));
else $HTMLOUT.= tr("{$lang['details_type']}", "None");
$HTMLOUT.= "<br><div style='display: flex' class='row'>
<div class='large-100 columns'>


  <button style='margin-left:auto;margin-right:auto;display: inline-block; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 28px; padding: 20px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer; margin: 5px;' type='button' class='block large-100' data-open = 'reveal_modal'>Read Torrent Description</button>

      <div style='width:85%;' class = 'reveal' id = 'reveal_modal' data-reveal>
         <table class='table'>
	<tr><td><b>{$lang['details_description']}</b></td></tr>
	<tr>
	<td>
	" . str_replace(array("\n","  ") , array("<br />\n","&nbsp; ") , format_comment($torrents_txt["descr"])) . "<!--</div>--></td></tr></table>

         <button class = 'close-button' data-close aria-label = 'Close reveal' type = 'button'>
            <span aria-hidden = 'true'>×</span>
         </button>
      </div></div>

      <script>
         $(document).ready(function() {
            $(document).foundation();
         })
      </script>";
if (in_array($torrents['category'], $INSTALLER09['movie_cats'])) {
    $HTMLOUT .= "
<div class='large-100 columns'>


  <button style='margin-left:auto;margin-right:auto;display: inline-block; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 28px; padding: 20px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer; margin: 5px;' type='button' class='block large-100' data-open = 'reveal_modal2'>OMDB Info</button>

      <div style='width:85%;' class = 'reveal' id = 'reveal_modal2' data-reveal>


<th class=' col-md-1 text-center'>
<div style='display:flex'>
<div style='width:30%;height:auto;'><img src='{$poster_url}'></div>
       <div style='width:60%'>
        </th><strong><font color='#79c5c5'>Title:</font></strong><font style='font-size:24px;' color='white'> " . $omdb['Title'] . "</font><br/>
        <strong><font color='#79c5c5'>Released:</font></strong><font color='grey'> " . $omdb['Released'] . "</font><br/>
        <strong><font color='#79c5c5'>Genre:</font></strong><font color='grey'> " . $omdb['Genre'] . "</font><br/>
	<strong><font color='#79c5c5'>Rated:</font></strong><font color='grey'> " . $omdb['Rated'] . "</font><br/>
	<strong><font color='#79c5c5'>Director:</font></strong><font color='grey'> " . $omdb['Director'] . "</font><br/>
	<strong><font color='#79c5c5'>Cast:</font></strong><font color='grey'> " . $omdb['Actors'] . "</font><br/>	<strong><font color='#79c5c5'>Description:</font></strong><font color='grey'> " . $omdb['Plot'] . "</font><br/>
    <strong><font color='#79c5c5'>Runtime:</font></strong><font color='grey'> " . $omdb['Runtime'] . "</font><br/>
    <strong><font color='#79c5c5'>IMDB Rating:</font></strong><span color='white'> " . $rating . "/10</span><span style='color:grey;'>&nbspfrom&nbsp&nbsp</span><span color='white'>" . $omdb['imdbVotes'] . "</span><span style='color:grey;'>&nbspVotes</span><br/><br/></div>
    </div></div></div></tr>

            <button class = 'close-button' data-close aria-label = 'Close reveal' type = 'button'>
            <span aria-hidden = 'true'>×</span>
         </button>
      

      <script>
         $(document).ready(function() {
            $(document).foundation();
         })
      </script>";
}

$torrents['tvcats'] = array(
    6,
    4,
    3,
    8
);



//tr($lang['details_tvrage'], $tvmaze_info, 1)
if (in_array($torrents['category'], $torrents['tvcats'])) {
    $tvmaze_info = tvmaze($torrents);
}
$tvmz = tr($lang['details_tvrage'], $tvmaze_info, 1);
//$HTMLOUT.="</div><iframe width='854' height='480' src='" . str_replace("watch?v=", "embed/", $torrents["youtube"]) . "' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen></iframe></div>";

if ($tvmaze_info) $HTMLOUT.= "<button style='margin-left:auto;margin-right:auto;display: inline-block; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 28px; padding: 20px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer; margin: 5px;' type='button' class='block large-100' data-open = 'tvmaze'>TV Show Info</button>

      <div style='width:85%;' class = 'reveal' id = 'tvmaze' data-reveal>
<div style='display:flex'>
<div style='padding:45px;display:flex;height:auto;'><img src='{$poster_url}'></div>
<div>{$tvmz}</div></td></tr>

<button class = 'close-button' data-close aria-label = 'Close reveal' type = 'button'>
            <span aria-hidden = 'true'>×</span>
         </button>
      </div></div>

      <script>
         $(document).ready(function() {
            $(document).foundation();
         })
      </script></div>";



if($trailer && $torrents["youtube"]) $HTMLOUT.="



  <button style='margin-left:auto;margin-right:auto;display: inline-block; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 28px; padding: 20px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer; margin: 5px;' type='button' class='block large-100' data-open = 'reveal_modal4'>Trailer</button>

      <div style='width:85%;' class = 'reveal' id = 'reveal_modal4' data-reveal>
<div><iframe style='margin-left:13%;text-align:center;width:95%;margin-left:16px;justify-content:center;' src='" . str_replace("watch?v=", "embed/", $torrents["youtube"]) . "' id=\"ytplayer\" type=\"text/html\" width=\"75%\" height=\"450px\" src=\"$trailer\" frameborder=\"0\"></iframe></div></td></tr>
<div><iframe style='margin-left:13%;text-align:center;width:95%;margin-left:16px;justify-content:center;' id=\"ytplayer\" type=\"text/html\" width=\"75%\" height=\"450px\" src=\"$trailer\" frameborder=\"0\"></iframe></div></td></tr>

<button class = 'close-button' data-close aria-label = 'Close reveal' type = 'button'>
            <span aria-hidden = 'true'>×</span>
         </button>
      </div></div>

      <script>
         $(document).ready(function() {
            $(document).foundation();
         })
      </script></div>";


 elseif($trailer) $HTMLOUT.= "



  <button style='margin-left:auto;margin-right:auto;display: inline-block; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 28px; padding: 20px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer; margin: 5px;' type='button' class='block large-100' data-open = 'reveal_modal3'>Trailer</button>

      <div style='width:85%;' class = 'reveal' id = 'reveal_modal3' data-reveal>
<div><iframe style='margin-left:13%;text-align:center;width:95%;margin-left:16px;justify-content:center;' id=\"ytplayer\" type=\"text/html\" width=\"75%\" height=\"450px\" src=\"$trailer\" frameborder=\"0\"></iframe></div></td></tr>

<button class = 'close-button' data-close aria-label = 'Close reveal' type = 'button'>
            <span aria-hidden = 'true'>×</span>
         </button>
      </div></div>

      <script>
         $(document).ready(function() {
            $(document).foundation();
         })
      </script></div>";

elseif($torrents["youtube"])$HTMLOUT.= "



  <button style='margin-left:auto;margin-right:auto;display: inline-block; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 28px; padding: 20px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer; margin: 5px;' type='button' class='block large-100' data-open = 'reveal_modal4'>Trailer</button>

      <div style='width:85%;' class = 'reveal' id = 'reveal_modal4' data-reveal>
<div><iframe style='margin-left:13%;text-align:center;width:95%;margin-left:16px;justify-content:center;' src='" . str_replace("watch?v=", "embed/", $torrents["youtube"]) . "' id=\"ytplayer\" type=\"text/html\" width=\"75%\" height=\"450px\" src=\"$trailer\" frameborder=\"0\"></iframe></div></td></tr>

<button class = 'close-button' data-close aria-label = 'Close reveal' type = 'button'>
            <span aria-hidden = 'true'>×</span>
         </button>
      </div></div>

      <script>
         $(document).ready(function() {
            $(document).foundation();
         })
      </script></div>";

elseif(!$trailer && !$torrents["youtube"]) $HTMLOUT.="</div>";


/**  Mod by dokty, rewrote by pdq  **/
$my_points = 0;
if (($torrent['torrent_points_'] = $mc1->get_value('coin_points_' . $id)) === false) {
    $sql_points = sql_query('SELECT userid, points FROM coins WHERE torrentid=' . sqlesc($id));
    $torrent['torrent_points_'] = array();
    if (mysqli_num_rows($sql_points) !== 0) {
        while ($points_cache = mysqli_fetch_assoc($sql_points)) $torrent['torrent_points_'][$points_cache['userid']] = $points_cache['points'];
    }
    $mc1->add_value('coin_points_' . $id, $torrent['torrent_points_'], 0);
}
$my_points = (isset($torrent['torrent_points_'][$CURUSER['id']]) ? (int)$torrent['torrent_points_'][$CURUSER['id']] : 0);
$HTMLOUT.= '<tr>
		<td class="heading" valign="top" align="right">Karma Points</td>
		<td valign="top" align="left"><b>In total ' . (int)$torrents['points'] . ' Karma Points given to this torrent of which ' . $my_points . ' from you.<br /><br />
		<a href="coins.php?id=' . $id . '&amp;points=10"><img src="' . $INSTALLER09['pic_base_url'] . '10coin.png" alt="10" title="10 Points" /></a>&nbsp;&nbsp;
		<a href="coins.php?id=' . $id . '&amp;points=20"><img src="' . $INSTALLER09['pic_base_url'] . '20coin.png" alt="20" title="20 Points" /></a>&nbsp;&nbsp;
		<a href="coins.php?id=' . $id . '&amp;points=50"><img src="' . $INSTALLER09['pic_base_url'] . '50coin.png" alt="50" title="50 Points" /></a>&nbsp;&nbsp;
		<a href="coins.php?id=' . $id . '&amp;points=100"><img src="' . $INSTALLER09['pic_base_url'] . '100coin.png" alt="100" title="100 Points" /></a>&nbsp;&nbsp;
		<a href="coins.php?id=' . $id . '&amp;points=200"><img src="' . $INSTALLER09['pic_base_url'] . '200coin.png" alt="200" title="200 Points" /></a>&nbsp;&nbsp;
		<a href="coins.php?id=' . $id . '&amp;points=500"><img src="' . $INSTALLER09['pic_base_url'] . '500coin.png" alt="500" title="500 Points" /></a>&nbsp;&nbsp;
		<a href="coins.php?id=' . $id . '&amp;points=1000"><img src="' . $INSTALLER09['pic_base_url'] . '1000coin.png" alt="1000" title="1000 Points" /></a></b>&nbsp;&nbsp;
		<br />By clicking on the coins you can give Karma Points to the uploader of this torrent.</td></tr>';
//=== Torrent free-o-meter (.) (.) == set the target amount for free leech
//=== get total points
//$mc1->delete_value('free_for_day_counter_'.$id);
//$mc1->delete_value('torrent_free_points_' . $id);

$HTMLOUT.= '<tr>
		<td class="heading" valign="top" align="right"><i><u>Free for one day</u></i></td><br /><br />
                <td class="heading" valign="top" align="left"><b>'.$INSTALLER09['torrent']['free_target'].'</b> points required to make this torrent free for 24 hours !<br /><br />'.$image_to_use.'<br /><br />
		<b>In total ' . (int)$torrents['f_points'] . ' Karma Points have been given to this torrent for 24 hours freeleech of which ' . $i_gave_points . ' from you.</b><br /><br />
		<a href="tf_points.php?id=' . $id . '&amp;tf_points=10">10</a>&nbsp;&nbsp;
		<a href="tf_points.php?id=' . $id . '&amp;tf_points=20">20</a>&nbsp;&nbsp;
		<a href="tf_points.php?id=' . $id . '&amp;tf_points=50">50</a>&nbsp;&nbsp;
		<a href="tf_points.php?id=' . $id . '&amp;tf_points=100">100</a>&nbsp;&nbsp;
		<a href="tf_points.php?id=' . $id . '&amp;tf_points=200">200</a>&nbsp;&nbsp;
		<a href="tf_points.php?id=' . $id . '&amp;tf_points=500">500</a>&nbsp;&nbsp;
		<br /><br />By clicking on the amounts you can give Karma Points making this torrent free for one day.<br /><br /></td></tr>';
// end
//==Vote for freeleech

$elapsed = $wait1 = 0;
$freepollby = $freepollby1 = '';
if ($CURUSER["class"] < UC_VIP) {
    $ratio1 = member_ratio($CURUSER['uploaded'], $CURUSER['downloaded']);
    if ($ratio1 < 0.55) $wait1 = 5;
    elseif ($ratio1 < 0.45) $wait1 = 10;
    elseif ($ratio1 < 0.35) $wait1 = 15;
    elseif ($ratio1 < 0.25) $wait1 = 20;
    elseif ($ratio1 < 0.15) $wait1 = 25;
    else $wait1 = 0;
}
$elapsed = floor((TIME_NOW - $torrents["added"]) / 3600);
$torrentid = (int)$torrents["id"];
$freepoll_sql = sql_query("SELECT userid FROM freepoll where torrentid=".sqlesc($torrentid)) or sqlerr(__FILE__, __LINE__);
$freepoll_all = mysqli_num_rows($freepoll_sql);
if ($freepoll_all) {
    while ($rows_t = mysqli_fetch_array($freepoll_sql)) {
        $freepoll_userid = (int)$rows_t["userid"];
        $user_sql = sql_query("SELECT id, username FROM users where id=".sqlesc($freepoll_userid)) or sqlerr(__FILE__, __LINE__);
        $rows_a = mysqli_fetch_array($user_sql);
        $username_t = htmlsafechars($rows_a["username"]);
        $freepollby1 = $freepollby1 . "<a href='{$INSTALLER09['baseurl']}/userdetails.php?id={$freepoll_userid}'>{$username_t}</a>, ";
    }
    $t_userid = (int)$CURUSER["id"];
    $tsqlcount = sql_query("SELECT COUNT(id) as tcount FROM freepoll where torrentid=".sqlesc($torrentid)) or sqlerr(__FILE__, __LINE__);
    $tass = mysqli_fetch_assoc($tsqlcount);
    $freepollcount = (int)$tass["tcount"];
    $tsql = sql_query("SELECT COUNT(id) FROM freepoll where torrentid=".sqlesc($torrentid)." and userid=".sqlesc($t_userid)) or sqlerr(__FILE__, __LINE__);
    $trows = mysqli_fetch_array($tsql);
    $t_ab = $trows[0];
    if ($t_ab == 0) {
        $freepollby = $freepollby . " <form action=\"freepoll.php\" method=\"post\">
        <input type=\"submit\" name=\"submit\" value=\"{$lang['details_freepoll_vote']}\" />
        <input type=\"hidden\" name=\"torrentid\" value=\"{$torrentid}\" />
        </form>";
    } else {
        $t_userid == (int)$torrents["owner"];
        $freepollby = $freepollby . " <form action=\"freepoll.php\" method=\"post\">
        <input type=\"submit\" name=\"submit\" value=\"{$lang['details_freepoll_voted']}\" disabled=\"disabled\" />
        <input type=\"hidden\" name=\"torrentid\" value=\"{$torrentid}\" />
        </form>";
    }
} else {
    $freepollcount = 0;
    $freepollby = "
        <form action=\"freepoll.php\" method=\"post\">
        <input type=\"submit\" name=\"submit\" value=\"{$lang['details_freepoll_vote']}\" />
        <input type=\"hidden\" name=\"torrentid\" value=\"{$torrentid}\" />
        </form>";
}
$count = $INSTALLER09['votes_required'] - $freepollcount;
if ($torrents["free"] == 0) {
    $HTMLOUT .= tr("{$lang['details_freepoll_poll']}", "".htmlsafechars($freepollcount)." {$lang['details_freepoll_mess1']}".htmlsafechars($count) . " {$lang['details_freepoll_mess2']}", 1);
}
if ($elapsed < $wait1 AND ($torrents["free"]) == 0)
    if ($t_ab >= 1 AND ($torrents["free"]) == 0) {
        if ($freepollcount < $INSTALLER09['votes_required'])
            $HTMLOUT .="<tr><td class='rowhead'><div align='right'>{$lang['details_freepoll_fvote']}</div></td><td align='left'>{$freepollby}</td></tr>";
    }
    else
        $HTMLOUT .="<tr><td class='rowhead'><div align='right'>{$lang['details_freepoll_sorry']}</div></td><td align='left'>{$lang['details_freepoll_mess3']} <a href='rules.php'><font color='red'><b>".number_format($wait1 - $elapsed)." {$lang['details_freepoll_hours']}</b></font></a> {$lang['details_freepoll_mess4']}</td></tr>";
elseif ($torrents["free"] == 0)
    $HTMLOUT .="<tr><td class='rowhead'><div align='right'>{$lang['details_freepoll_vote']}</div></td><td align='left'>{$freepollby}Each vote costs <b>{$INSTALLER09['torrent']['freepoll_points']}</b> karma points</td></tr>";
$tid = (int)$torrents["id"];
$Free_Dur = (TIME_NOW + 604800); //== 1 Week
if ($freepollcount == $INSTALLER09['votes_required'] && $torrents["free"] >= 1) {
    $HTMLOUT .="<tr><td class='rowhead'><div align='right'>{$lang['details_freepoll_poll']}</div></td><td align='left'>{$lang['details_freepoll_mess5']}</td></tr>";
}
if ($freepollcount == $INSTALLER09['votes_required'] && $torrents["free"] == 0) {
    sql_query("UPDATE torrents SET free = ".($Free_Dur)." WHERE torrents.id=" .sqlesc($tid)) or sqlerr(__FILE__, __LINE__);
    $mc1->begin_transaction('torrent_details_' . $torrentid);
    $mc1->update_row(false, array(
        'free' => $Free_Dur
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['torrent_details']);
    //$mc1->delete_value('torrent_details_' . $torrentid);
}


if ($torrents['free'] >= 1 || $torrents['freetorrent'] >= 1 || $isfree['yep'] || $free_slot OR $double_slot != 0 || $CURUSER['free_switch'] != 0) {
    $HTMLOUT.= "<tr>
		<td align='right' class='heading'>Ratio After Download</td>
		<td class='details-text-ellipsis'><del>{$sr}&nbsp;&nbsp;Your new ratio if you download this torrent.</del> <b><font size='' color='#FF0000'>[FREE]</font></b>&nbsp;(Only upload stats are recorded)</td></tr>";
} else {
    $HTMLOUT.= "<tr>
		<td align='right' class='heading'>Ratio After Download</td>
		<td>{$sr}&nbsp;&nbsp;Your new ratio if you download this torrent.</td></tr>";
}
//==End
$HTMLOUT.= "<div class='row'>
<div class='large-100 columns'>";
//== Similar Torrents mod
$searchname = substr($torrents['name'], 0, 6);
$query1 = str_replace(" ", ".", sqlesc("%" . $searchname . "%"));
$query2 = str_replace(".", " ", sqlesc("%" . $searchname . "%"));
if (($sim_torrents = $mc1->get_value('similiar_tor_' . $id)) === false) {
    $r = sql_query("SELECT id, name, size, added, seeders, leechers, category FROM torrents WHERE name LIKE {$query1} AND id <> " . sqlesc($id) . " OR name LIKE {$query2} AND id <> " . sqlesc($id) . " ORDER BY name") or sqlerr(__FILE__, __LINE__);
    while ($sim_torrent = mysqli_fetch_assoc($r)) $sim_torrents[] = $sim_torrent;
    $mc1->cache_value('similiar_tor_' . $id, $sim_torrents, 86400);
}
if (count($sim_torrents) > 0) {
    $sim_torrent = "<table class='table'>\n" . "
		<thead>
		<tr>
		<th>Type</th>
		<th>Name</th>
		<th>Size</th>
		<th>Added</th>
		<th>Seeders</th>
		<th>Leechers</th>
		</tr>
		</thead>\n";
    if ($sim_torrents) {
        foreach ($sim_torrents as $a) {
            $sim_tor['cat_name'] = htmlsafechars($change[$a['category']]['name']);
            $sim_tor['cat_pic'] = htmlsafechars($change[$a['category']]['image']);
            $cat = "<img src=\"pic/caticons/{$CURUSER['categorie_icon']}/{$sim_tor['cat_pic']}\" alt=\"{$sim_tor['cat_name']}\" title=\"{$sim_tor['cat_name']}\" />";
            $name = htmlsafechars(CutName($a["name"]));
            $seeders = (int)$a["seeders"];
            $leechers = (int)$a["leechers"];
            $added = get_date($a["added"], 'DATE', 0, 1);
            $sim_torrent.= "<tr>
			<td class='one' style='padding: 0px; border: none' width='40px'>{$cat}</td>
			<td class='one'><a href='details.php?id=" . (int)$a["id"] . "&amp;hit=1'><b>{$name}</b></a></td>
			<td class='one' style='padding: 1px' align='center'>" . mksize($a['size']) . "</td>
			<td class='one' style='padding: 1px' align='center'>{$added}</td>
			<td class='one' style='padding: 1px' align='center'>{$seeders}</td>
			<td class='one' style='padding: 1px' align='center'>{$leechers}</td></tr>\n";
        }
        $sim_torrent.= "</table>";
 $HTMLOUT.= "<table class='table'><tr><td align='right' class='heading'>{$lang['details_similiar']}<a href=\"javascript: klappe_news('a5')\"><img border=\"0\" src=\"pic/plus.png\" id=\"pica5".(int)$a['id']."\" alt=\"[Hide/Show]\" title=\"[Hide/Show]\" /></a><div id=\"ka5\" style=\"display: none;\"><br />$sim_torrent</div></td></tr></table>";
    } else {
        if (empty($sim_torrents)) $HTMLOUT.= "
		<table class='table'>\n
		<tr>
		<td>Nothing similiar to " . htmlsafechars($torrents["name"]) . " found.</td>
		</tr></table>";
    }
}

$HTMLOUT.= "<br>
<div class='row'>
<div class='large-100 columns'>
<table align='center' class='table table-bordered span3'>\n";
//==subs by putyn
if (in_array($torrents["category"], $INSTALLER09['movie_cats']) && !empty($torrents["subs"])) {
    $HTMLOUT.= "<tr>
		<td class='rowhead'>Subtitles</td>
		<td align='left'>";
    $subs_array = explode(",", $torrents["subs"]);
    foreach ($subs_array as $k => $sid) {
        require_once (CACHE_DIR . 'subs.php');
        foreach ($subs as $sub) {
            if ($sub["id"] == $sid) $HTMLOUT.= "<img border=\"0\" width=\"25px\" style=\"padding:3px;\"src=\"" . htmlsafechars($sub["pic"]) . "\" alt=\"" . htmlsafechars($sub["name"]) . "\" title=\"" . htmlsafechars($sub["name"]) . "\" />";
        }
    }
    $HTMLOUT.= "</td></tr>\n";
}

/*$HTMLOUT.= tr("Rating", getRate($id, "torrent") , 1);
// --------------- likes start------
        $att_str = '';
        if (!empty($torrents['user_likes'])) {
            $likes = explode(',', $torrents['user_likes']);
        } else {
            $likes = '';
        }
        if (!empty($likes) && count(array_unique($likes)) > 0) {
            if (in_array($CURUSER['id'], $likes)) {
                if (count($likes) == 1) {
                    $att_str = jq('You like this');
                } elseif (count(array_unique($likes)) > 1) {
                    $att_str = jq('You and&nbsp;') . ((count(array_unique($likes)) - 1) == '1' ? '1 other person likes this' : (count($likes) - 1) . '&nbsp;others like this');
                }
            } elseif (!(in_array($CURUSER['id'], $likes))) {
                if (count(array_unique($likes)) == 1) {
                    $att_str = '1 other person likes this';
                } elseif (count(array_unique($likes)) > 1) {
                    $att_str = (count(array_unique($likes))) . '&nbsp;others like this';
                }
            }
        }
        $wht = ((!empty($likes) && count(array_unique($likes)) > 0 && in_array($CURUSER['id'], $likes)) ? 'unlike' : 'like');

$HTMLOUT.= tr("Likes","<span id='mlike' data-com='" . (int)$torrents["id"] . "' class='details {$wht}'>[" . ucfirst($wht) . "]</span><span class='tot-" . (int)$torrents["id"] . "' data-tot='" . (!empty($likes) && count(array_unique($likes)) > 0 ? count(array_unique($likes)) : '') . "'>&nbsp;{$att_str}</span>" , 1);
$HTMLOUT.= tr("{$lang['details_last_seeder']}", "{$lang['details_last_activity']}" . get_date($l_a['lastseed'], '', 0, 1));
$HTMLOUT.= tr("{$lang['details_size']}", mksize($torrents["size"]) . " (" . number_format($torrents["size"]) . " {$lang['details_bytes']})");
$HTMLOUT.= tr("{$lang['details_added']}", get_date($torrents['added'], "{$lang['details_long']}"));
//Display pretime
    if ($pretime['time'] == '0') {
    $prestatement = "No pretime found.";
    } else {
    $prestatement = get_pretime(time() -  $pretime['time']) . " ago<br />Uploaded " . get_pretime($torrents['added'] - $pretime['time']) . " after pre.";
    }
$HTMLOUT.="<tr><td align='right' class='heading'>Pre Time</td><td width='99%' align='left'>". $prestatement."</td></tr>";
$HTMLOUT.= tr("{$lang['details_views']}", (int)$torrents["views"]);
$HTMLOUT.= tr("{$lang['details_hits']}", (int)$torrents["hits"]);
$Ocelot_Or_Default = (OCELOT_TRACKER == true ? 'snatches_ocelot.php?id=' : 'snatches.php?id=');
$HTMLOUT.= tr("{$lang['details_snatched']}", ($torrents["times_completed"] > 0 ? "<a href='{$INSTALLER09["baseurl"]}/{$Ocelot_Or_Default}{$id}'>{$torrents['times_completed']} {$lang['details_times']}</a>" : "0 {$lang['details_times']}") , 1);
$HTMLOUT.= "
<script type='text/javascript'>
function showme() {
    document.getElementById('show').innerHTML = '{$CURUSER['username']} is viewing details for torrent {$INSTALLER09['baseurl']}/details.php?id=" . (int)$torrents['id'] . "\"';
}
</script>
<tr><td class='rowhead'>Status update</td><td><button type='button' class='small button'  id='show' onclick='showme()'>DO IT!</button></td></tr>";
*/
$HTMLOUT.= "</table></div><!-- closing col md 4 -->
<div class='large-100 columns'>
<table align='center' class='table table-bordered'>";
//==Report Torrent Link
$HTMLOUT.= tr("Report Torrent", "<form action='report.php?type=Torrent&amp;id=$id' method='post'><input class='small button' type='submit' name='submit' value='Report This Torrent' />&nbsp;&nbsp;<strong><em class='label label-primary'>For breaking the&nbsp;<a href='rules.php'>rules</a></em></strong></form>", 1);
//== Tor Reputation by pdq
if ($torrent_cache['rep'] && $INSTALLER09['rep_sys_on']) {
    $torrents = array_merge($torrents, $torrent_cache['rep']);
    $member_reputation = get_reputation($torrents, 'torrents', $torrents['anonymous']);
    $HTMLOUT.= '<tr><td class="heading" valign="top" align="right" width="1%">Reputation</td>
		<td align="left" width="99%">' . $member_reputation . ' (counts towards uploaders Reputation)<br /></td></tr>';
}
//==Anonymous
$rowuser = (isset($torrents['username']) ? ("<a href='userdetails.php?id=" . (int)$torrents['owner'] . "'><b>" . htmlsafechars($torrents['username']) . "</b></a>") : "{$lang['details_unknown']}");
$uprow = (($torrents['anonymous'] == 'yes') ? ($CURUSER['class'] < UC_STAFF && $torrents['owner'] != $CURUSER['id'] ? '' : $rowuser . ' - ') . "<i>{$lang['details_anon']}</i>" : $rowuser);
if ($CURUSER >= UC_STAFF) $uprow.= " $spacer<$editlink><b>{$lang['details_edit']}</b></a>";
$HTMLOUT.= tr("Upped by", $uprow, 1);
//==pdq's Torrent Moderation
if ($CURUSER['class'] >= UC_STAFF) {
    if (!empty($torrents['checked_by'])) {
        if (($checked_by = $mc1->get_value('checked_by_' . $id)) === false) {
            $checked_by = mysqli_fetch_assoc(sql_query("SELECT id FROM users WHERE username=" . sqlesc($torrents['checked_by']))) or sqlerr(__FILE__, __LINE__);
            $mc1->add_value('checked_by_' . $id, $checked_by, 30 * 86400);
        }
        $HTMLOUT.= "<tr>
	<td class='rowhead'>Checked by</td>
	<td align='left'>
<p><a class='label label-primary' href='{$INSTALLER09["baseurl"]}/userdetails.php?id=" . (int)$checked_by['id'] . "'>
	<strong>" . htmlsafechars($torrents['checked_by']) . "</strong></a></p>
	<p><a href='{$INSTALLER09["baseurl"]}/details.php?id=" . (int)$torrents['id'] . "&amp;rechecked=1'>
        <small><em class='label label-primary'><strong>[Re-Check this torrent]</strong></em></small></a> 
	<a href='{$INSTALLER09["baseurl"]}/details.php?id=" . (int)$torrents['id'] . "&amp;clearchecked=1'>
	<small><em class='label label-primary'><strong>[Un-Check this torrent]</strong></em></small></a></p>
	&nbsp;<p><em class=label label-primary'>* STAFF Eyes Only *</em>
	".(isset($torrents["checked_when"]) && $torrents["checked_when"] > 0 ? "<strong>Checked When : ".get_date($torrents["checked_when"],'DATE',0,1)."</strong>":'' )."</td></tr>";
    } else {

        $HTMLOUT.= "<tr><td class='rowhead'>Checked by</td><td align='left'><em class='label label-primary'><strong>NOT CHECKED!</strong></em> 
       <a href='{$INSTALLER09["baseurl"]}/details.php?id=" . (int)$torrents['id'] . "&amp;checked=1'>
       <em class='label label-primary'><small><strong>[Check this torrent]</strong></small></em></a>&nbsp;<em class='label label-primary'><strong>* STAFF Eyes Only *</strong></em></p></td></tr>";
    }
}
// end
if ($torrents["type"] == "multi") {
    if (!isset($_GET["filelist"])) $HTMLOUT.= tr("{$lang['details_num_files']}<a href=\"./filelist.php?id=$id\" class=\"sublink\"><br />{$lang['details_list']}</a>", (int)$torrents["numfiles"] . " files", 1);
    else {
        $HTMLOUT.= tr("{$lang['details_num-files']}", (int)$torrents["numfiles"] . "{$lang['details_files']}", 1);
    }
}

if(OCELOT_TRACKER == true) {
$HTMLOUT.= tr("{$lang['details_peers']}<br /><a href=\"./peerlist_ocelot.php?id=$id#seeders\" class=\"sublink\">{$lang['details_list']}</a>", (int)$torrents_ocelot["seeders"] . "{$lang['details_add_sd']}" . (int)$torrents_ocelot["leechers"] . "{$lang['details_add_lc']}" . ((int)$torrents_ocelot["seeders"] + (int)$torrents_ocelot["leechers"]) . "{$lang['details_peer_total']}", 1);
} else {
$HTMLOUT.= tr("{$lang['details_peers']}<br /><a href=\"./peerlist.php?id=$id#seeders\" class=\"sublink\">{$lang['details_list']}</a>", (int)$torrents["seeders"] . "{$lang['details_add_sd']}" . (int)$torrents["leechers"] . "{$lang['details_add_lc']}" . ((int)$torrents["seeders"] + (int)$torrents["leechers"]) . "{$lang['details_peer_total']}", 1);
}
//==putyns thanks mod
$HTMLOUT.= tr($lang['details_thanks'], '
	  <script type="text/javascript">
		/*<![CDATA[*/
		$(document).ready(function() {
			var tid = '.$id.';
			show_thanks(tid);
		});
		
		/*]]>*/
		
		</script>
		<noscript><iframe id="thanked" src ="thanks.php?torrentid='.$id.'" style="width:500px;height:50px;border:none;overflow:auto;">
	  <p>Your browser does not support iframes. And it has Javascript disabled!</p>
	  
	  </iframe></noscript>
	  <div id="thanks_holder"></div>', 1);
//==End
//==09 Reseed by putyn
$next_reseed = 0;
if ($torrents["last_reseed"] > 0) $next_reseed = ($torrents["last_reseed"] + 172800); //add 2 days
$reseed = "<form method=\"post\" action=\"./takereseed.php\">
	  <select name=\"pm_what\">
	  <option value=\"last10\">last10</option>
	  <option value=\"owner\">uploader</option>
	  </select>
	  <input class\"button\" type=\"submit\"  " . (($next_reseed > TIME_NOW) ? "disabled='disabled'" : "") . " value=\"SendPM\" />
	  <input type=\"hidden\" name=\"uploader\" value=\"" . (int)$torrents["owner"] . "\" />
	  <input type=\"hidden\" name=\"reseedid\" value=\"$id\" />
	  </form>";
$HTMLOUT.= tr("Request reseed", $reseed, 1);
//==End
//$HTMLOUT.= "</table></div><!-- closing col md 8 --></div><!-- closing row -->";
//$HTMLOUT.= "</div> <!-- closing tab pane -->";
if ($CURUSER['class'] >= UC_POWER_USER) {
//$HTMLOUT .="<div class='tabs-panel' id='tab_d'>";

$HTMLOUT.= "
<h3 class='text-center'>{$lang['details_add_snatch1']}<a href='{$INSTALLER09['baseurl']}/details.php?id=" . (int)$torrents['id'] . "'>" . htmlsafechars($torrents['name']) . "</a><br />{$lang['details_add_snatch2']}{$Row_Count['0']}{$lang['details_add_snatch3']}" . ($Row_Count[0] == 1 ? "" : "es") . "</h3>\n";


 //== \\0//
 if (OCELOT_TRACKER == true) {
    $snatched_torrent = "
<table class='table'>
<tr>
<td class='colhead' align='left'>{$lang['details_snatches_username']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_uploaded']}</td>
" . ($INSTALLER09['ratio_free'] ? "" : "<td class='colhead' align='right'>{$lang['details_snatches_downloaded']}</td>") . "
<td class='colhead' align='right'>{$lang['details_snatches_ratio']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_seedtime']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_leechtime']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_lastaction']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_completedat']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_announced']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_active']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_completed']}</td>
</tr>\n";
    } else {
    $snatched_torrent = "
<table class='table'>
<tr>
<td class='colhead' align='left'>{$lang['details_snatches_username']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_connectable']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_uploaded']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_upspeed']}</td>
" . ($INSTALLER09['ratio_free'] ? "" : "<td class='colhead' align='right'>{$lang['details_snatches_downloaded']}</td>") . "
" . ($INSTALLER09['ratio_free'] ? "" : "<td class='colhead' align='right'>{$lang['details_snatches_downspeed']}</td>") . "
<td class='colhead' align='right'>{$lang['details_snatches_ratio']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_completed']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_seedtime']}</td>
<td class='colhead' align='right'>{$lang['details_snatches_leechtime']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_lastaction']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_completedat']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_client']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_port']}</td>
<td class='colhead' align='center'>{$lang['details_snatches_announced']}</td>
</tr>\n";
}

    if ($Detail_Snatch) {
        foreach ($Detail_Snatch as $D_S) {

if (OCELOT_TRACKER == true) {
           //== \\0//
           $ratio = ($D_S["downloaded"] > 0 ? number_format($D_S["uploaded"] / $D_S["downloaded"], 3) : ($D_S["uploaded"] > 0 ? "Inf." : "---"));
           $active = ($D_S['active'] == 1 ? $active = "<img src='" . $INSTALLER09['pic_base_url'] . "aff_tick.gif' alt='Yes' title='Yes' />" : $active = "<img src='" . $INSTALLER09['pic_base_url'] . "aff_cross.gif' alt='No' title='No' />");
           $completed = ($D_S['tstamp'] >= 1 ? $completed = "<img src='" . $INSTALLER09['pic_base_url'] . "aff_tick.gif' alt='Yes' title='Yes' />" : $completed = "<img src='" . $INSTALLER09['pic_base_url'] . "aff_cross.gif' alt='No' title='No' />");
           $snatchuserocelot = (isset($D_S['username2']) ? ("<a href='userdetails.php?id=" . (int)$D_S['uid'] . "'><b>" . htmlsafechars($D_S['username2']) . "</b></a>") : "{$lang['details_snatches_unknown']}");
           $username_ocelot = (($D_S['anonymous2'] == 'yes' OR $D_S['paranoia'] >= 2) ? ($CURUSER['class'] < UC_STAFF && $D_S['uid'] != $CURUSER['id'] ? '' : $snatchuserocelot . ' - ') . "<i>{$lang['details_snatches_anon']}</i>" : $snatchuserocelot);
           $snatched_torrent.= "<tr>
                                 <td align='left'><font size='2%'>{$username_ocelot}</font></td>
                                 <td align='right'><font size='2%'>" . mksize($D_S["uploaded"]) . "</font></td>
  " . ($INSTALLER09['ratio_free'] ? "" : "<td align='right'><font size='2%'>" . mksize($D_S["downloaded"]) . "</font></td>") . "
                                 <td align='right'><font size='2%'>" . htmlsafechars($ratio) . "</font></td>
                                 <td align='right'><font size='2%'>" . mkprettytime($D_S["seedtime"]) . "</font></td>
                                 <td align='right'><font size='2%'>" . mkprettytime($D_S["leechtime"]) . "</font></td>
                                 <td align='center'><font size='2%'>" . get_date($D_S["mtime"], '', 0, 1) . "</font></td>
                                 <td align='center'><font size='2%'>" . get_date($D_S["completedtime"], '', 0, 1) . "</font></td>
                                 <td align='center'><font size='2%'>" . (int)$D_S["announced"] . "</font></td>
                                 <td align='center'><font size='2%'>" . $active . "</font></td>
                                 <td align='center'><font size='2%'>" . $completed . "</font></td>
        </tr>\n";

} else {
 $upspeed = ($D_S["upspeed"] > 0 ? mksize($D_S["upspeed"]) : ($D_S["seedtime"] > 0 ? mksize($D_S["uploaded"] / ($D_S["seedtime"] + $D_S["leechtime"])) : mksize(0)));
           $downspeed = ($D_S["downspeed"] > 0 ? mksize($D_S["downspeed"]) : ($D_S["leechtime"] > 0 ? mksize($D_S["downloaded"] / $D_S["leechtime"]) : mksize(0)));
    $ratio = ($D_S["downloaded"] > 0 ? number_format($D_S["uploaded"] / $D_S["downloaded"], 3) : ($D_S["uploaded"] > 0 ? "Inf." : "---"));
           $completed = sprintf("%.2f%%", 100 * (1 - ($D_S["to_go"] / $D_S["size"])));
           $snatchuser = (isset($D_S['username2']) ? ("<a href='userdetails.php?id=" . (int)$D_S['userid'] . "'><b>" . htmlsafechars($D_S['username2']) . "</b></a>") : "{$lang['details_snatches_unknown']}");
           $username = (($D_S['anonymous2'] == 'yes' OR $D_S['paranoia'] >= 2) ? ($CURUSER['class'] < UC_STAFF && $D_S['userid'] != $CURUSER['id'] ? '' : $snatchuser . ' - ') . "<i>{$lang['details_snatches_anon']}</i>" : $snatchuser);
$snatched_torrent.= "<tr>
                                 <td align='left'><font size='2%'>{$username}</font></td>
                                 <td align='center'><font size='2%'>" . ($D_S["connectable"] == "yes" ? "<font color='green'>{$lang['details_add_yes']}</font>" : "<font color='red'>{$lang['details_add_no']}</font>") . "</font></td>
                                 <td align='right'><font size='2%'>" . mksize($D_S["uploaded"]) . "</font></td>
                                 <td align='right'><font size='2%'>" . htmlsafechars($upspeed) . "/s</font></td>
  " . ($INSTALLER09['ratio_free'] ? "" : "<td align='right'><font size='2%'>" . mksize($D_S["downloaded"]) . "</font></td>") . "
  " . ($INSTALLER09['ratio_free'] ? "" : "<td align='right'><font size='2%'>" . htmlsafechars($downspeed) . "/s</font></td>") . "
                                 <td align='right'><font size='2%'>" . htmlsafechars($ratio) . "</font></td>
                                 <td align='right'><font size='2%'>" . htmlsafechars($completed) . "</font></td>
                                 <td align='right'><font size='2%'>" . mkprettytime($D_S["seedtime"]) . "</font></td>
                                 <td align='right'><font size='2%'>" . mkprettytime($D_S["leechtime"]) . "</font></td>
                                 <td align='center'><font size='2%'>" . get_date($D_S["last_action"], '', 0, 1) . "</font></td>
                                 <td align='center'><font size='2%'>" . get_date($D_S["complete_date"], '', 0, 1) . "</font></td>
                                 <td align='center'><font size='2%'>" . htmlsafechars($D_S["agent"]) . "</font></td>
                                 <td align='center'><font size='2%'>" . (int)$D_S["port"] . "</font></td>
                                 <td align='center'><font size='2%'>" . (int)$D_S["timesann"] . "</font></td>
        </tr>\n";
        }

}

$snatched_torrent.= "</table>";
$HTMLOUT.= "
<p class='text-center'>{$lang['details_add_snatch4']}</p>
<div class='panel-body'>
        <div class='panel-group' id='accordion'>
            <div id='collapseOne' class='panel-collapse collapse in'>
                <div class='panel'>
                    <div class='panel-body'>$snatched_torrent</div></div></div>";
} else {
 if (empty($Detail_Snatch)) $HTMLOUT.= "<p class='text-center'>{$lang['details_add_snatch4']}</p>
<div class='panel-body'>
        <div class='panel-group' id='accordion'>
            <div id='collapseOne' class='panel-collapse collapse in'>
                <div class='panel'>
                    <div class='panel-body'><h3 class=text-center'>{$lang['details_add_snatch5']}</h3></div></div></div>";
   }
}


///if (empty($tvmaze_info) && empty($omdb) && in_array($torrents['category'], array_merge($INSTALLER09['movie_cats'], $INSTALLER09['tv_cats']))) $HTMLOUT.= "<tr><td colspan='2'>No Imdb or TVMaze info.</td></tr>";


/// zokii1983 add pictures customised bt swizzles
$HTMLOUT .= "<div class='container'><div class='row' style='background:transparent !important;'><div class='large-12 columns'>";
$HTMLOUT .= "<div class='panel panel-default'>";
$HTMLOUT .= "<div class='panel-heading'><h2 class='text-center'>SCREENSHOTS</h2></div>";
$HTMLOUT .= "<div class='panel-body'>";
if ($CURUSER['class'] >= UC_UPLOADER) {
$HTMLOUT .= '<div class="screenup_bg">
<form text-center" name="addImages" id="addImagesForm" action="imageUpload.php?id='.$id.'" method="post" enctype="multipart/form-data">   
<div class="row">
<div class="large-2 columns"><label for="images"><b>Select Images</b></label></div>
<div class="col-md-6"><input class="/*form-control*/" type="file" id="upload" name="upload[]" multiple=""></div>
<div class="col-md-3 col-md-pull-0"><button type="submit" class="small button">Upload</button></div>
</div>
</form></div><br>';
}

/// zokii1983 display pictures customised by swizzles

$HTMLOUT.="<table class='table table-bordered'><tr style='background:rgba(0,0,0,.5);'>";
$files = glob("pictures/".$id."/*.*");

for ($i=0, $iMax = count($files); $i< $iMax; $i++) {
$HTMLOUT.="<td class='screenup_col text-center'>";
    $image = $files[$i];
	$end = end((explode('/', rtrim($image, '/'))));

    $HTMLOUT .= '<!--<a href="'.$image .'">--><img src="' . $INSTALLER09["baseurl"] . '/pictures/'.$id.'/thumbs/'.$end.'""><!--</a>-->';
	if ($CURUSER['class'] >= UC_UPLOADER) {
$HTMLOUT .= '<form method="post">
  <input type="hidden" value="'.$image .'" name="delete_file">
  <input type="submit" value="Delete image">
</form>';
}
$HTMLOUT.="</td>";
}
$HTMLOUT.= "</tr></table>";

//Modal for all pictures
$files2 = glob("pictures/".$id."/*.*");
$image2 = $files2;
if (!empty($files2)) {
$HTMLOUT.="<div class='row'><div class='large-12 columns text-center'><a href='#myModal1' class='small button' data-toggle='modal'>To see all the full size images click here</a></div></div><br><br>";
$HTMLOUT.='<!--begin modal window-->
<div class="modal fade" id="myModal1">
<div class="modal-dialog" style="width:95% !important;">
<div class="modal-content">
<div class="modal-header">
<div class="text-center">SCREENSHOTS</div>
</div>';

$HTMLOUT .= '<div class="modal-body">     
  <div id="lightbox" class="carousel slide" data-ride="carousel"><ul class="carousel-indicators"> ';
$count = 1;
 foreach ($image2 as $newimage){
if ($count == 1) $HTMLOUT .= '<li class="active" data-target="#lightbox">';
else $HTMLOUT .= '<li data-target="#lightbox">';
$HTMLOUT .='</li>';
$count++;
}
$HTMLOUT .= '</ul>';
$HTMLOUT .= '<div class="carousel-inner">';
                $count = 1;
                foreach ($image2 as $newimage){
if ($count == 1) $HTMLOUT .= '<div class="item active">';
else $HTMLOUT .= '<div class="item">';
$HTMLOUT .= '<img src="'.$newimage.'"  class="img-responsive det_carousel"></div>';
              $count++;
              }

            $HTMLOUT .= '       
</div><!--carousel-inner-->
     <a class="left carousel-control" href="#lightbox" role="button" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
    <a class="right carousel-control" href="#lightbox" role="button" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
    </div><!--lightbox div-->
<br><button class="small button close" type="button" data-dismiss="modal">Close&nbsp;&times;</button> <br> 
</div>';
 $HTMLOUT .= '</div><!--end carousel-->
</div><!--end modal-body-->
</div><!--end modal-content-->
</div><!--end modal-dialogue-->
</div><!--end myModal-->';
}else{
$HTMLOUT.= '<h3 class="text-center">NO SCREENSHOTS HAVE BEEN UPLOADED</h3>';
 $HTMLOUT .= '</div><!--end carousel-->
</div><!--end modal-body-->
</div><!--end modal-content-->
</div><!--end modal-dialoge-->
</div><!--end myModal-->';
}

//== End of screenshots
$HTMLOUT.= "<hr /><br /> 
<h2 class='text-center'>{$lang['details_comments']}<a href='details.php?id=$id'>" . htmlsafechars($torrents["name"], ENT_QUOTES) . "</a></h2>";
$HTMLOUT.= "<div class='container'><div class='row'>
<div class='large-2 columns'></div>
<div class='large-12 columns'>
<p><a name='startcomments'></a></p>
    <form name='comment' method='post' action='comment.php?action=add&amp;tid=$id'>
    <table align='center'>
    <tr>
    <td align='center'><b>{$lang['details_quick_comment']}</b></td></tr>
    <tr><td align='center'>
    <textarea name='body'></textarea>
    <input type='hidden' name='tid' value='" . htmlsafechars($id) . "' /><br />
    <a href=\"javascript:SmileIT(':-)','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/smile1.gif' alt='Smile' title='Smile' /></a> 
    <a href=\"javascript:SmileIT(':smile:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/smile2.gif' alt='Smiling' title='Smiling' /></a> 
    <a href=\"javascript:SmileIT(':-D','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/grin.gif' alt='Grin' title='Grin' /></a> 
    <a href=\"javascript:SmileIT(':lol:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/laugh.gif' alt='Laughing' title='Laughing' /></a> 
    <a href=\"javascript:SmileIT(':w00t:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/w00t.gif' alt='W00t' title='W00t' /></a> 
    <a href=\"javascript:SmileIT(':blum:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/blum.gif' alt='Rasp' title='Rasp' /></a> 
    <a href=\"javascript:SmileIT(';-)','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/wink.gif' alt='Wink' title='Wink' /></a> 
    <a href=\"javascript:SmileIT(':devil:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/devil.gif' alt='Devil' title='Devil' /></a> 
    <a href=\"javascript:SmileIT(':yawn:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/yawn.gif' alt='Yawn' title='Yawn' /></a> 
    <a href=\"javascript:SmileIT(':-/','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/confused.gif' alt='Confused' title='Confused' /></a> 
    <a href=\"javascript:SmileIT(':o)','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/clown.gif' alt='Clown' title='Clown' /></a> 
    <a href=\"javascript:SmileIT(':innocent:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/innocent.gif' alt='Innocent' title='innocent' /></a> 
    <a href=\"javascript:SmileIT(':whistle:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/whistle.gif' alt='Whistle' title='Whistle' /></a> 
    <a href=\"javascript:SmileIT(':unsure:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/unsure.gif' alt='Unsure' title='Unsure' /></a> 
    <a href=\"javascript:SmileIT(':blush:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/blush.gif' alt='Blush' title='Blush' /></a> 
    <a href=\"javascript:SmileIT(':hmm:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/hmm.gif' alt='Hmm' title='Hmm' /></a> 
    <a href=\"javascript:SmileIT(':hmmm:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/hmmm.gif' alt='Hmmm' title='Hmmm' /></a> 
    <a href=\"javascript:SmileIT(':huh:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/huh.gif' alt='Huh' title='Huh' /></a> 
    <a href=\"javascript:SmileIT(':look:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/look.gif' alt='Look' title='Look' /></a> 
    <a href=\"javascript:SmileIT(':rolleyes:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/rolleyes.gif' alt='Roll Eyes' title='Roll Eyes' /></a> 
    <a href=\"javascript:SmileIT(':kiss:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/kiss.gif' alt='Kiss' title='Kiss' /></a> 
    <a href=\"javascript:SmileIT(':blink:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/blink.gif' alt='Blink' title='Blink' /></a> 
    <a href=\"javascript:SmileIT(':baby:','comment','body')\"><img border='0' src='{$INSTALLER09['pic_base_url']}smilies/baby.gif' alt='Baby' title='Baby' /></a><br />
    <input class='small button' type='submit' value='Submit' /></td></tr></table></form></div><!-- closing col md 8 --></div><!-- closing row --></div><!-- closing container -->";
if ($torrents["allow_comments"] == "yes" || $CURUSER['class'] >= UC_STAFF && $CURUSER['class'] <= UC_MAX) {
    $HTMLOUT.= "\n";
} else {
    $HTMLOUT.= "
	<p><table align='center' class='table table-bordered'>
	<tr>
	<td><a name='startcomments'>&nbsp;</a><b>{$lang['details_com_disabled']}</b></td>
	</tr>
        </table></p>
     </div>
     </div><div class='row'><div class='col-md-1'></div><div class='col-md-10'>\n";
    echo stdhead("{$lang['details_details']}\"" . htmlsafechars($torrents["name"], ENT_QUOTES) . "\"", true, $stdhead) . $HTMLOUT . stdfoot(true, $stdfoot);
    die();
}
$commentbar = " 
<div class='row'>
<div class='large-2 columns'></div>
<div class='large-4 columns'>
<div class='content'><br><p align='center' ><a  class='index' href='comment.php?action=add&amp;tid=$id'>{$lang['details_add_comment']}</a>
    <br /><a class='index' href='{$INSTALLER09['baseurl']}/takethankyou.php?id=" . $id . "'>
    <img src='{$INSTALLER09['pic_base_url']}smilies/thankyou.gif' alt='Thanks' title='Thank You' border='0' /></a></p></div>
     </div>
   <div class='row'>
<div class='large-2 columns'></div>
<div class='col-md-8'>\n";
$count = (int)$torrents['comments'];
if (!$count) {
    $HTMLOUT.= "
<div class='container'>
<div class='row'>
<div class='col-md-6 col-md-offset-5'>
<h2>{$lang['details_no_comment']}</h2>\n";
} else {
    $perpage = 15;
    $pager = pager($perpage, $count, "details.php?id=$id&amp;", array(
        'lastpagedefault' => 1
    ));
    $subres = sql_query("SELECT comments.id, comments.text, comments.user_likes, comments.user, comments.torrent, comments.added, comments.anonymous, comments.editedby, comments.editedat, comments.edit_name, users.warned, users.enabled, users.chatpost, users.leechwarn, users.pirate, users.king, users.perms, users.avatar, users.av_w, users.av_h, users.offavatar, users.warned, users.reputation, users.opt1, users.opt2, users.mood, users.username, users.title, users.class, users.donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = " . sqlesc($id) . " ORDER BY comments.id " . $pager['limit']) or sqlerr(__FILE__, __LINE__);
    $allrows = array();
    while ($subrow = mysqli_fetch_assoc($subres)) $allrows[] = $subrow;
    $HTMLOUT.="
     </div>
     </div>
     </div>
<div class='row'>
<div class='col-md-3'></div>
<div class='col-md-8'>";
$HTMLOUT.="<br><div class='col-sm-offset-3'><div style='display:inline-block;width:0%;'></div><button type='button' class='small button' data-toggle='collapse' data-target='#dropdown'>Open/Close comments</button></div><br><div id='dropdown' class='collapse in'>";
$HTMLOUT.= $commentbar;
    $HTMLOUT.= $pager['pagertop'];
    $HTMLOUT.= commenttable($allrows);
    $HTMLOUT.= $pager['pagerbottom'];
    $HTMLOUT.="</div></div></div></div><br>";
}
 $HTMLOUT.="</div></div><div class='row'><div class='col-md-1'></div><div class='col-md-10'>";
//////////////////////// HTML OUTPUT ////////////////////////////
?>
