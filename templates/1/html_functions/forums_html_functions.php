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
 /// zoki forums start

///// forum header with collapse /////
function begin_f_main_div(){    return "<div class='card'>";}
// begin head div
function begin_f_head_div(){    return "<div class='card-divider'>";}
// head label with image on right
function begin_f_head_label($x){    return "<span class='nav navbar-nav navbar-right'><label for='checkbox_4' class='text-left'>";}
// head lebel with no image
function begin_f_head_label_noimage($x){    return "<label for='checkbox_4' class='text-left'>";}
// image to use to collapse
function collapse_image(){    return "<img src='templates/1/pic/carbon/collapse.png' alt='[-]' title='[-/+]' />";}
// end head label with image on right
function end_f_head_label(){    return "</label></span>";}
// end head label with no image
function end_f_head_label_noimage(){    return "</label>";}
// end head div
function end_f_head_div(){    return "</div>";}
// begin body div
function begin_f_body_div($x){    return "<div class='card-section'>";}
/// forum tables, tr, td
function begin_f_main_table(){    return "<table class='table'>"; }
function begin_thead(){    return "<thead>";}
function begin_tr(){    return "<tr>";}
function begin_td_head($x,$y)
{    return "<td class='thead' align='$x' colspan='$y'>";}
function end_tdtr(){    return "</td></tr>";}
function end_thead(){    return "</td></tr>";}
///// end forum tables, tr, td

function end_f_body_div(){    return "</div>";}
function end_f_main_div(){    return "</div>";}
////////////// end forum header with collapse ///////

// button at the bottom of the page
function begin_f_buttons_div(){    return "<div class='row'><div class='small-12 columns col-sm-offset-0'>";}
function begin_f_buttons_align(){    return "<p align='text-center'>";}
function end_f_buttons_align(){    return "</p>";}
function end_f_buttons_div(){    return "</div></div>";}
// forums end
?>