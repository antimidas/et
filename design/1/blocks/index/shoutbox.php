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
//==Start activeusers - pdq & shoutbox 09
$keys['activeusers'] = 'activeusers';
if (($active_users_cache = $mc1->get_value($keys['activeusers'])) === false) {
    $dt = $_SERVER['REQUEST_TIME'] -  180;
    $activeusers = '';
    $active_users_cache = array();
    $res = sql_query('SELECT id, username, class, donor, title, warned, enabled, chatpost, leechwarn, pirate, king, perms ' . 'FROM users WHERE last_access >= ' . $dt . ' ' . 'AND perms < ' . bt_options::PERMS_STEALTH . ' ORDER BY last_access DESC') or sqlerr(__FILE__, __LINE__);
    $actcount = mysqli_num_rows($res);
    $v = ($actcount != 1 ? 's' : '');
    while ($arr = mysqli_fetch_assoc($res)) {
        if ($activeusers) $activeusers.= "<br>";
        $activeusers.= '<b>' . format_username($arr) . '</b>';
    }
    $active_users_cache['activeusers'] = $activeusers;
    $active_users_cache['actcount'] = $actcount;
    $active_users_cache['au'] = number_format($actcount);
    $last24_cache['v'] = $v;
    $mc1->cache_value($keys['activeusers'], $active_users_cache, $INSTALLER09['expires']['activeusers']);
}
if (!$active_users_cache['activeusers']) $active_users_cache['activeusers'] = $lang['index_active_users_no'];

if ($CURUSER['opt1'] & user_options::SHOW_SHOUT) {
    $commandbutton = $refreshbutton = $smilebutton = $custombutton = $staffsmiliebutton = '';
    if ($CURUSER['class'] >= UC_STAFF) {
        $staffsmiliebutton .= "<a style='margin-top:5px;margin-left:auto;margin-right:auto;display: inline; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 16px; padding: 10px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer;' class='tiny button' href=\"javascript:PopStaffSmiles('shbox','shbox_text')\">{$lang['index_shoutbox_ssmilies']}</a>";
    }
    if (get_smile() != 0) $custombutton .= "
<a style='margin-top:5px;margin-left:auto;margin-right:auto;display: inline; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 16px; padding: 10px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer;' href=\"javascript:PopCustomSmiles('shbox','shbox_text')\">{$lang['index_shoutbox_csmilies']}</a>";
    if ($CURUSER['class'] >= UC_STAFF) {
        $commandbutton = "<a style='margin-top:5px;margin-left:auto;margin-right:auto;display: inline; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 16px; padding: 10px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer;' class='tiny button' href=\"javascript:popUp('shoutbox_commands.php')\">{$lang['index_shoutbox_commands']}</a>\n";
    }
    $refreshbutton = "<a style='margin-top:5px;margin-left:auto;margin-right:auto;display: inline; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 16px; padding: 10px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer;' class='tiny button' href='shoutbox.php' target='shoutbox'>{$lang['index_shoutbox_refresh']}</a>\n";

    $activenow = "<button style='margin-top:5px;margin-left:auto;margin-right:auto;display: inline; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 16px; padding: 10px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer;' type='button' class='block small-12' data-open = 'reveal_modal'>Users Online<span class='badge success disabled' style='color:#fff'>" . $active_users_cache['actcount'] . "</span></button>



      <div class = 'reveal' id = 'reveal_modal' data-reveal>
         
         <label class='text-left'>" . $lang['index_active'] . "&nbsp;&nbsp;<span class='badge success disabled' style='color:#fff'>" . $active_users_cache['actcount'] . "</span></label>
  <div class='callout' style='overflow:auto; height:555px;'><p>" . $active_users_cache['activeusers'] . "</p></div>

         <button class = \"close-button\" data-close aria-label = \"Close reveal\" type = \"button\">
            <span aria-hidden = \"true\">×</span>
         </button>
      </div>";


 /*   $smilebutton = "<button style='margin-top:5px;margin-left:auto;margin-right:auto;width:auto;display: inline; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 16px; padding: 10px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer;' type='button' class='block small-12' data-open = 'reveal_msmiles'>More Smiles</button>



      <div class = 'reveal' id = 'reveal_msmiles' data-reveal>
         <iframe style='border: 0px;width:90%;height:auto;'>

         <button class = \"close-button\" data-close aria-label = \"Close reveal\" type = \"button\">
            <span aria-hidden = \"true\">×</span>
         </button>
      </div>
      <script>
         $(document).ready(function() {
            $(document).foundation();
         })
      </script>";*/

    $smilebutton = "<a style='margin-top:5px;margin-left:auto;margin-right:auto;display: inline; background-color: #000C22AB; border-radius: 10px; border: 4px double #cccccc; color: #eeeeee; text-align: center; font-size: 16px; padding: 10px; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -o-transition: all 0.5s; transition: all 0.5s; cursor: pointer;' href=\"javascript:PopMoreSmiles('shbox','shbox_text')\">{$lang['index_shoutbox_smilies']}</a>\n";


    $HTMLOUT .= "<div class='card'>
	<div class='card-divider portlet-header'>{$lang['index_shoutbox_general']}</div>
  <div class='portlet-content card-section'>";
    $HTMLOUT .= "
<div class='large-100 columns'>
<form action='shoutbox.php' method='get' target='shoutbox' name='shbox' onsubmit='mysubmit()'>
<iframe src='{$INSTALLER09['baseurl']}/shoutbox.php' class='shout-table' name='shoutbox'></iframe>
		<div class='input-group'>
			<input type='text' class='input-group-field' name='shbox_text' placeholder='Shout Text'>
			<div class='input-group-button'>
			<input class='button' type='submit' value='{$lang['index_shoutbox_send']}'>
			<input type='hidden' name='sent' value='yes'>
			</div>
		</div>
</div>
<!--<div class='large-3 columns'>-->
	<!--<label class='text-left'>" . $lang['index_active'] . "&nbsp;&nbsp;<span class='badge success disabled' style='color:#fff'>" . $active_users_cache['actcount'] . "</span></label>-->
  <!--<div class='callout' style='overflow:auto; height:555px;'><p>" . $active_users_cache['activeusers'] . "</p></div></div>-->
<div  class='large-12 columns'>
<div style='display:table;margin-left:auto;margin-right:auto;'>
<a href=\"javascript:SmileIT(':-)','shbox','shbox_text')\"><i class='em em-laughing'></i></a>
<a href=\"javascript:SmileIT(':thumbu:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/thumbu.png' alt='Baby' title='Baby' /></a>
<a href=\"javascript:SmileIT(':smile:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/smile2.gif' alt='Smiling' title='Smiling' /></a>
<a href=\"javascript:SmileIT(':-D','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/grin.gif' alt='Grin' title='Grin' /></a>
<a href=\"javascript:SmileIT(':lol:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/laugh.gif' alt='Laughing' title='Laughing' /></a>
<a href=\"javascript:SmileIT(':w00t:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/w00t.gif' alt='W00t' title='W00t' /></a>
<a href=\"javascript:SmileIT(':blum:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/blum.gif' alt='Rasp' title='Rasp' /></a>
<a href=\"javascript:SmileIT(';-)','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/wink.gif' alt='Wink' title='Wink' /></a>
<a href=\"javascript:SmileIT(':devil:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/devil.gif' alt='Devil' title='Devil' /></a>
<a href=\"javascript:SmileIT(':yawn:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/yawn.gif' alt='Yawn' title='Yawn' /></a>
<a href=\"javascript:SmileIT(':-/','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/confused.gif' alt='Confused' title='Confused' /></a>
<a href=\"javascript:SmileIT(':o)','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/clown.gif' alt='Clown' title='Clown' /></a>
<a href=\"javascript:SmileIT(':innocent:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/innocent.gif' alt='Innocent' title='innocent' /></a>
<a href=\"javascript:SmileIT(':whistle:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/whistle.gif' alt='Whistle' title='Whistle' /></a>
<a href=\"javascript:SmileIT(':unsure:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/unsure.gif' alt='Unsure' title='Unsure' /></a>
<a href=\"javascript:SmileIT(':blush:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/blush.gif' alt='Blush' title='Blush' /></a>
<a href=\"javascript:SmileIT(':hmm:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/hmm.gif' alt='Hmm' title='Hmm' /></a>
<a href=\"javascript:SmileIT(':hmmm:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/hmmm.gif' alt='Hmmm' title='Hmmm' /></a>
<a href=\"javascript:SmileIT(':huh:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/huh.gif' alt='Huh' title='Huh' /></a>
<a href=\"javascript:SmileIT(':look:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/look.gif' alt='Look' title='Look' /></a>
<a href=\"javascript:SmileIT(':rolleyes:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/rolleyes.gif' alt='Roll Eyes' title='Roll Eyes' /></a>
<a href=\"javascript:SmileIT(':kiss:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/kiss.gif' alt='Kiss' title='Kiss' /></a>
<a href=\"javascript:SmileIT(':blink:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/blink.gif' alt='Blink' title='Blink' /></a>
<a href=\"javascript:SmileIT(':baby:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/baby.gif' alt='Baby' title='Baby' /></a>
<a href=\"javascript:SmileIT(':wank:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/wan.gif' alt='Baby' title='Baby' /></a>
<a href=\"javascript:SmileIT(':thumbd:','shbox','shbox_text')\"><img src='{$INSTALLER09['pic_base_url']}smilies/thumbd.png' alt='Baby' title='Baby' /></a></br>


</div>
<div>".$activenow."</div><br>
<div  style='margin-left:auto;margin-right:auto;display: flex'>".$smilebutton.$custombutton."";
    if ($CURUSER['class'] >= UC_STAFF)
    {
        $HTMLOUT.= ''.$staffsmiliebutton.'';
        }
$HTMLOUT.="</div>";


        $HTMLOUT .= "</div></form>";
        $HTMLOUT .= "</div></div></div><br>";


}


if (!($CURUSER['opt1'] & user_options::SHOW_SHOUT)) {
   $HTMLOUT.= "<div class='row'><ul class='accordion' data-accordion data-multi-expand='true'>
  <li class='accordion-item is-active' data-accordion-item><b><a href='#' class='accordion-title'>{$lang['index_shoutbox']}</b></a><a class='button' type='button' href='{$INSTALLER09['baseurl']}/shoutbox.php?show_shout=1&amp;show=yes'><div class='accordion-content' data-tab-content>{$lang['index_shoutbox_open']}&nbsp;</a></div></li></ul></div>";
}
//==end 09 shoutbox
//==End
// End Class
// End File
?>
