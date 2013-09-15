<?php

/*
Plugin Name: Twitter real time search scrolling
Plugin URI: http://www.gopiplus.com/work/2010/07/18/twitter-real-time-search/
Description: This plug-in will scroll the most recent twittered contents from your twitter account. 
Author: Gopi.R
Version: 7.0
Author URI: http://www.gopiplus.com/work/2010/07/18/twitter-real-time-search/
Donate link: http://www.gopiplus.com/work/2010/07/18/twitter-real-time-search/
Tags: Rss, plugin, wordpress, slider
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

global $wpdb, $wp_version;
define("TwitterRealURL", "http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=##USERNAME##");

function twitterreal_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script( 'twitter-real-time-search-scrolling', get_option('siteurl').'/wp-content/plugins/twitter-real-time-search-scrolling/twitter-real-time-search-scrolling.js');
	}	
}

function twitterreal_activation() 
{
	global $wpdb;
	add_option('twitterreal_title', "Twitter real time");
	add_option('twitterreal_username', "gopiplus");
	add_option('twitterreal_height', "50");
	add_option('twitterreal_display', "2");
	add_option('twitterreal_length', "150");
}

function twitterreal_admin_options() 
{
	global $wpdb;
	?>
	<div class="wrap">
    <h2>Twitter real time search scrolling (Widget Setting)</h2>
    </div>
	<?php
	$twitterreal_title = get_option('twitterreal_title');
	$twitterreal_username = get_option('twitterreal_username');
	$twitterreal_height = get_option('twitterreal_height');
	$twitterreal_display = get_option('twitterreal_display');
	$twitterreal_length = get_option('twitterreal_length');
	
	if (@$_POST['twitterreal_submit']) 
	{
		$twitterreal_title = stripslashes($_POST['twitterreal_title']);
		$twitterreal_username = stripslashes($_POST['twitterreal_username']);
		$twitterreal_height = stripslashes($_POST['twitterreal_height']);
		$twitterreal_display = stripslashes($_POST['twitterreal_display']);
		$twitterreal_length = stripslashes($_POST['twitterreal_length']);
		
		update_option('twitterreal_title', $twitterreal_title );
		update_option('twitterreal_username', $twitterreal_username );
		update_option('twitterreal_height', $twitterreal_height );
		update_option('twitterreal_display', $twitterreal_display );
		update_option('twitterreal_length', $twitterreal_length );
	}
	
	?>
	<form name="crs_form" method="post" action="">
	<?php
	echo '<p>Widget title:<br><input  style="width: 200px;" type="text" value="';
	echo $twitterreal_title . '" name="twitterreal_title" id="twitterreal_title" /></p>';
	
	echo '<p>Twitter username:<br>@<input  style="width: 200px;" type="text" value="';
	echo $twitterreal_username . '" name="twitterreal_username" id="twitterreal_username" /></p>';
	
	echo '<p>Each scroller height in scroll:<br><input  style="width: 212px;" type="text" value="';
	echo $twitterreal_height . '" name="twitterreal_height" id="twitterreal_height" /> </p>';
	
	echo '<p>Display number of record at the same time in scroll:<br> <input  style="width: 212px;" type="text" value="';
	echo $twitterreal_display . '" name="twitterreal_display" id="twitterreal_display" /></p>';
	
	echo '<p>Enter max twitt character:<br> <input  style="width: 212px;" type="text" value="';
	echo $twitterreal_length . '" name="twitterreal_length" id="twitterreal_length" /></p>';

	echo '<input name="twitterreal_submit" id="twitterreal_submit" lang="publish" class="button-primary" value="Update Setting" type="Submit" />';
	?>
	</form>
	<br />
	<div class="wrap">
	<strong>Plugin Configuration</strong>
	<ol>
		<li>Drag and drop the widget</li>
		<li>Add directly in the theme</li>
		<li>Short code for pages and posts</li>
	</ol>
	Check official website for more information <a href="http://www.gopiplus.com/work/2010/07/18/twitter-real-time-search/" target="_blank">click here</a>
	</div>
	<?php
}

function twitterreal_shortcode( $atts ) 
{
	global $wpdb;
	//[twitter-real-scrolling username="gopiplus" height="30" display="3"]
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	$username = $atts['username'];
	$height = $atts['height'];
	$display = $atts['display'];
	$textlength = get_option('twitterreal_length');
	
	$url = str_replace("##USERNAME##", $username, TwitterRealURL);

	if(!is_numeric(@$textlength)){ @$textlength = 250; }
	if(!is_numeric(@$display)){ @$display = 2; }
	if(!is_numeric(@$height)){ @$height = 50; }
	
	$xml = "";
	$cnt=0;
	$f = fopen( $url, 'r' );
	while( $data = fread( $f, 4096 ) ) { $xml .= $data; }
	fclose( $f );
	preg_match_all( "/\<item\>(.*?)\<\/item\>/s", $xml, $itemblocks );

	if ( ! empty($itemblocks) ) 
	{
		$twitterreal_count = 0;
		$twitterreal_html = "";
		$IRjsjs = "";
		$twitterreal_x = "";
		foreach( $itemblocks[1] as $block )
		{
			$twitterreal_target = "_blank";
			
			preg_match_all( "/\<title\>(.*?)\<\/title\>/",  $block, $title );
			preg_match_all( "/\<link\>(.*?)\<\/link\>/", $block, $link );
			preg_match_all( "/\<description\>(.*?)\<\/description\>/", $block, $description );
			
			$twitterreal_title = $title[1][0];
			$twitterreal_title = addslashes(trim($twitterreal_title));
			$twitterreal_link = $link[1][0];
			$twitterreal_link = trim($twitterreal_link);
			$twitterreal_text = addslashes(trim($description[1][0]));
			$twitterreal_text = str_replace("&lt;![CDATA[","",$twitterreal_text);
			$twitterreal_text = str_replace("<![CDATA[","",$twitterreal_text);
			$twitterreal_text = str_replace("]]&gt;","",$twitterreal_text);
			$twitterreal_text = str_replace("]]>","",$twitterreal_text);
			
			if(is_numeric($textlength))
			{
				if($textlength <> "" && $textlength > 0 )
				{
					$twitterreal_text = substr($twitterreal_text, 0, $textlength);
				}
			}
			
			$twitterreal_scrollheights = $height."px";	
			
			$twitterreal_html = $twitterreal_html . "<div class='twitterreal_div' style='height:".$twitterreal_scrollheights.";padding:1px 0px 1px 0px;'>"; 
					
			$twitterreal_text = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $twitterreal_text);
			$twitterreal_text = str_replace($username.":", '', $twitterreal_text);
			$rest = substr(trim($twitterreal_text), -1); 

			if ($rest == ":")
			{
				$twitterreal_text = substr(trim($twitterreal_text), 0, -1);
			}
			
			if($twitterreal_text <> "" )
			{		
				$twitterreal_html = $twitterreal_html . "<div style='padding-left:4px;'>$twitterreal_text";
				$twitterreal_html = $twitterreal_html . "<a target='_blank' href='$twitterreal_link'>...</a>"; 
				$twitterreal_html = $twitterreal_html . "</div>";
				
				$IRjsjs = $IRjsjs . "<div style=\'padding-left:4px;\'>$twitterreal_text ";	
				$IRjsjs = $IRjsjs . "<a target=\'_blank\' href=\'$twitterreal_link\'>...</a>";
				$IRjsjs = $IRjsjs . "</div>";
			}
			
			$twitterreal_html = $twitterreal_html . "</div>";
			
			$twitterreal_x = $twitterreal_x . "TwitterrealSlider[$twitterreal_count] = '<div class=\'twitterreal_div\' style=\'height:".$twitterreal_scrollheights.";padding:1px 0px 1px 0px;\'>$IRjsjs</div>'; ";	
			$twitterreal_count++;
			$IRjsjs = "";
		}
		
		$height = $height + 4;
		if($twitterreal_count >= $display)
		{
			$twitterreal_count = $display;
			$twitterreal_scrollheight_New = ($height * $display);
		}
		else
		{
			$twitterreal_count = $twitterreal_count;
			$twitterreal_scrollheight_New = ($twitterreal_count  * $height);
		}
	}
	
	$TwitterrealSlider = "";
	$TwitterrealSlider = $TwitterrealSlider . '<div style="padding-top:8px;padding-bottom:8px;">';
	$TwitterrealSlider = $TwitterrealSlider . '<div style="text-align:left;vertical-align:middle;text-decoration: none;overflow: hidden; position: relative; margin-left: 3px; height: '. @$height .'px;" id="TwitterrealSlider">'.@$twitterreal_html.'</div>';
	$TwitterrealSlider = $TwitterrealSlider . '</div>';
	$TwitterrealSlider = $TwitterrealSlider . '<script type="text/javascript">';
	$TwitterrealSlider = $TwitterrealSlider . 'var TwitterrealSlider = new Array();';
	$TwitterrealSlider = $TwitterrealSlider . "var objTwitterrealSlider	= '';";
	$TwitterrealSlider = $TwitterrealSlider . "var twitterreal_scrollPos 	= '';";
	$TwitterrealSlider = $TwitterrealSlider . "var twitterreal_numScrolls	= '';";
	$TwitterrealSlider = $TwitterrealSlider . 'var twitterreal_heightOfElm = '. $height. ';';
	$TwitterrealSlider = $TwitterrealSlider . 'var twitterreal_numberOfElm = '. $twitterreal_count. ';';
	$TwitterrealSlider = $TwitterrealSlider . "var twitterreal_scrollOn 	= 'true';";
	$TwitterrealSlider = $TwitterrealSlider . 'function TwitterrealSliderScroll() ';
	$TwitterrealSlider = $TwitterrealSlider . '{';
	$TwitterrealSlider = $TwitterrealSlider . $twitterreal_x;
	$TwitterrealSlider = $TwitterrealSlider . "objTwitterrealSlider	= document.getElementById('TwitterrealSlider');";
	$TwitterrealSlider = $TwitterrealSlider . "objTwitterrealSlider.style.height = (twitterreal_numberOfElm * twitterreal_heightOfElm) + 'px';";
	$TwitterrealSlider = $TwitterrealSlider . 'TwitterrealSliderContent();';
	$TwitterrealSlider = $TwitterrealSlider . '}';
	$TwitterrealSlider = $TwitterrealSlider . '</script>';
	$TwitterrealSlider = $TwitterrealSlider . '<script type="text/javascript">';
	$TwitterrealSlider = $TwitterrealSlider . 'TwitterrealSliderScroll();';
	$TwitterrealSlider = $TwitterrealSlider . '</script>';
	return $TwitterrealSlider;
}

function twitterreal_add_to_menu() 
{
	if (is_admin()) 
	{
		add_options_page('Twitter real time search scrolling', 'Twitter real time search scrolling', 'manage_options', __FILE__, 'twitterreal_admin_options' );
	}
}

function twitterreal_deactivation() 
{
	// No action required.
}

function twitterreal($username = 'gopiplus') 
{
	$arr = array();
	$arr["username"] = $username;
	$arr["height"] = get_option('twitterreal_height');
	$arr["display"] = get_option('twitterreal_display');
	echo twitterreal_shortcode($arr);
}

function twitterreal_widget($args) 
{
	extract($args);
	echo $before_widget . $before_title;
	echo get_option('twitterreal_title');
	echo $after_title;
	
	// [twitter-real-scrolling username="gopiplus" height="30" display="3"]
	$arr = array();
	$arr["username"] = get_option('twitterreal_username');
	$arr["height"] = get_option('twitterreal_height');
	$arr["display"] = get_option('twitterreal_display');
	echo twitterreal_shortcode($arr);
	
	echo $after_widget;
}

function twitterreal_control()
{
	echo 'Twitter real time search scrolling';
}

function twitterreal_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('twitter-real-time-search-scrolling', 'Twitter real time search scrolling', 'twitterreal_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('twitter-real-time-search-scrolling', array('Twitter real time search scrolling', 'widgets'), 'twitterreal_control');
	} 
}

add_action("plugins_loaded", "twitterreal_init");
add_shortcode( 'twitter-real-scrolling', 'twitterreal_shortcode' );
register_activation_hook(__FILE__, 'twitterreal_activation');
register_deactivation_hook(__FILE__, 'twitterreal_deactivation');
add_action('admin_menu', 'twitterreal_add_to_menu');
add_action('wp_enqueue_scripts', 'twitterreal_add_javascript_files');
?>