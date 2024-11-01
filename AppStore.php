<?php
/*
Plugin Name: AppStore Links
Plugin URI: http://tirolercast.ste-bi.net/wordpress-plugins/appstore-plugin/
Description: A filter for WordPress that displays AppstoreDetails
Version:4.5.2
Author: Stephan
Author URI: http://www.ste-bi.net

Instructions

Copy the folder that contains this file  unzipped into the wp-content/plugins folder of WordPress, 
then go to Administration > Plugins, it should be in the list. Activtate it 
and avery occurence of the expression [app itemId] (case sensitive) will 
and avery occurence of the expression [app itemId] (case sensitive) will 
embed an Appstore AppLogo, Name, Price, aso.

Thanks to Manuel, Alexander and Dave who helped me developing this Plugin
*/
if ( !defined('ABSPATH') ) {
	require_once ( '../../../wp-blog-header.php');
}

include ("AppFunctions.php");
include ("definitions.php");
include ("widget.php");
@include ("searchwidget.php");

wp_enqueue_style('thickbox'); 
wp_enqueue_script('jquery');  
wp_enqueue_script('thickbox'); 


function appstore_defaults_array() {
	$defaults = array(
		'id'=> '',
		'screenshots'=>false);
	return $defaults;
} 

function appstore_process($atts, $content=null, $code="" ) {
	@$a = shortcode_atts(appstore_defaults_array(), $atts );
	extract($a);
	
	$showScreenshots = 0;
		
	if ($screenshots==true) {
		$showScreenshots = 1;
	}
	
	// old System
	if ($id=="") {
		$id = $atts[0];
	}
	
	if ($content<>null){
		return appstore_getCustom($id, $content);
	}	
	
	if ($id<>"") {
		return AppStoreLinks_getContent($id,$showScreenshots);
	}
}


function appstore_process_ext($atts, $content=null, $code="" ) {
	$showScreenshots = 1;
	
	$id = $atts[0];
	
	if ($id<>"") {
		return AppStoreLinks_getContent($id,$showScreenshots);
	}
}

function appstore_process_img($atts, $content=null, $code="" ) {
	$showScreenshots = 2;
	
	$id = $atts[0];
	
	if ($id<>"") {
		return AppStoreLinks_getContent($id,$showScreenshots);
	}
}
	
function appstore_getCustom($searchid, $content) {
	list( $obj , $spanOverlay ) = getContent($searchid);

	if ($obj==false) {
		return  "Product not found";
	}
		
	// Variablen
	$AppStore_country = get_option("AppStore_country");
	$trackName = $obj->results[0]->trackName;
	$formattedPrice = $obj->results[0]->formattedPrice;

	$sellerName = $obj->results[0]->sellerName;
	$sellerUrl = $obj->results[0]->sellerUrl;
	$contentAdvisoryRating = $obj->results[0]->contentAdvisoryRating;
	$description = str_replace("\n", "<br />", $obj->results[0]->description);
	$AffLink = WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY."/AppStore.php?appid=".$searchid;
	$DLLink = '<a href="'.$AffLink.'" rel="nofollow" target="_blank" >'.APPSTORE_DL_LINKNAME.'</a>';
	$ratingHTML = '<span class="apps">'.getRatingStars($obj).'</span>';
	$artwork100 = $obj->results[0]->artworkUrl100;
	$artwork60 = $obj->results[0]->artworkUrl60;
	$artwork512 = $obj->results[0]->artworkUrl512;
	$averageUserRating = $obj->results[0]->averageUserRating;
	$averageUserRatingForCurrentVersion = $obj->results[0]->averageUserRatingForCurrentVersion;
	$releaseNotes = $obj->results[0]->releaseNotes;
	
	// {trackname}, {sellername}, {dllink}, {price}, {stars}, {description}, {artwork100}, {artwork60}

	$content = str_replace("{id}",$id,$content);
	$content = str_replace("{trackname}",$trackName,$content);
	$content = str_replace("{sellername}",$sellerName,$content);
	$content = str_replace("{dllink}",$DLLink,$content);
	$content = str_replace("{affurl}",$AffLink,$content);
	$content = str_replace("{price}",$formattedPrice,$content);
	$content = str_replace("{stars}",$ratingHTML,$content);
	$content = str_replace("{description}",$description,$content);
	$content = str_replace("{artwork100}",$artwork100,$content);
	$content = str_replace("{artwork60}",$artwork60,$content);
	$content = str_replace("{artwork512}",$artwork512,$content);
	$content = str_replace("{averageuserrating}",$averageUserRating,$content);
	$content = str_replace("{averageuserratingforcurrentversion}",$averageUserRatingForCurrentVersion,$content);
	$content = str_replace("{releasenotes}",$releaseNotes,$content);
	
	return $content;
}

function getRatingStars($obj,$class="") {
	//  Get Rating
	
	$averageUserRatingForCurrentVersion = $obj->results[0]->averageUserRatingForCurrentVersion;
	$userRatingCountForCurrentVersion = $obj->results[0]->userRatingCountForCurrentVersion;
	$averageUserRating = $obj->results[0]->averageUserRating;
	$userRatingCount = $obj->results[0]->userRatingCount;
	
	// not enough ratings
	if (is_null($averageUserRatingForCurrentVersion)==true){
		$averageUserRatingForCurrentVersion = 0;
		$userRatingCountForCurrentVersion = "not enough";
	}
	// not enough ratings
	if (is_null($averageUserRating)==true){
		$averageUserRating = 0;
		$userRatingCount = "not enough";
	}			
	
	//'#container li {background: url(stars.png) no-repeat top left;}'

	if ($averageUserRatingForCurrentVersion>0){
			if ($class<>"") {
				$class = 'class="'.$class.'"';
			}	
			
			 $ratingHTML = '<img '.$class.' src="'.WP_PLUGIN_URL.
						'/appstore/images/stars'.$averageUserRatingForCurrentVersion.
						'.png" alt="'.$averageUserRatingForCurrentVersion.
						'" title = "'.$userRatingCountForCurrentVersion.' Ratings" >'; 
			/*$ratingClass = 'sprite-stars'.str_replace('.','',$averageUserRatingForCurrentVersion ) ;					
			$ratingHTML = '<div class="appStars '.$ratingClass.'" title="'.$userRatingCountForCurrentVersion.' Ratings" 
							alt="'.$averageUserRatingForCurrentVersion.' Ratings"></div>';*/										
						
		} else {
			$ratingHTML = '';//nicht gen&uuml;gend Bewertungen';
		}					
	return $ratingHTML;
}

function AppStoreLinks_getContent($searchid,$type) {	
	list( $obj , $spanOverlay ) = getContent($searchid);
	
	if ($obj==false) {
		return  "Artikel wurde nicht gefunden";
	}

	//Keine Maske bei App-Software
	if ($obj->results[0]->kind=="mac-software") {
		$isMacSoftware = 1;
	} else {
		$isMacSoftware = 0;
	}
	// Alternative
	if ($obj->results[0]->genreIds[0]>=12000) {
		$isMacSoftware = 1;
	} else {
		$isMacSoftware = 0;
	}
	
	
	
	$output = "";
	
	if ($type < 2) {	
		// Read Content
		if ($isMacSoftware == 1) {
			$artworkUrl60 = $obj->results[0]->artworkUrl100;
		} else {
			$artworkUrl60 = $obj->results[0]->artworkUrl60;
		}
		$artworkUrl60 = $obj->results[0]->artworkUrl60;
		$trackName = $obj->results[0]->trackName;
		$price = $obj->results[0]->price;
		$AppStore_country = get_option("AppStore_country");
		$language = get_option("AppStore_language");		
		$currency = $obj->results[0]->currency;
		$formattedPrice = $obj->results[0]->formattedPrice;
		
		
		$sellerName = $obj->results[0]->sellerName;
		$sellerUrl = $obj->results[0]->sellerUrl;
		$contentAdvisoryRating = $obj->results[0]->contentAdvisoryRating;
		$description = str_replace("\n", "<br />", $obj->results[0]->description);
		

		$cacheimageurl = getImage($searchid, $artworkUrl60, $isMacSoftware);
		

		$AffLink = WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY."/AppStore.php?appid=".$searchid;
		
		// ToDo: Checken ob Thickbox installiert ist
		if (1==2) {
			$ThickURL = esc_url($AffLink.'&#'.$searchid.';TB_iframe=true&#'.$searchid.';width=800&#'.$searchid.';height=800');
			$ThickBoxLink = '<a href="'.$ThickURL.'" rel="nofollow" class="thickbox" title="'.APPSTORE_DL_LINKNAME.'">';
		} else {
			$ThickBoxLink = '<a href="'.$AffLink.'" rel="nofollow" target="_blank" >';
		} 
			
		// Update Benachrichtigung, aber nur wenn nicht aus Appstore entfernt 
		$UpdateTimeSpan = time() - (3*24*60*60); // 3Tage
		$releaseDate = strtotime($obj->results[0]->releaseDate);
	

		if (($releaseDate >= $UpdateTimeSpan) && ($spanOverlay == "")) {
			$spanOverlay = '<span style="display: block; width: 54px; height: 54px; position: absolute; margin-top: 27px; margin-left: 27px; z-index: 20;  background: transparent url('.WP_PLUGIN_URL.'/appstore/images/update.png) center center no-repeat;"></span>';
		}
 
		// Language Options
	
		if ($language == "de_de") {
			$langDeveloper = "Hersteller:";
			$langPrice = "Preis:";
			$langAgeRating = "Freigabe:";
			$langRating = "Bewertung:";
		} else {
			$langDeveloper = __("Developer:","appstore");
			$langPrice = __("Price:","appstore");
			$langAgeRating = __("Rated:","appstore");
			$langRating = __("Rating:","appstore");
		}
	 	
	 	if (get_option("AppStore_showRatings")=='checked'){
			$ratingHTML = getRatingStars($obj,'ratingStarsImg');						
		} else {
			$ratingHTML = "";
		}
		
		// Create Output 
		$output .= '<span class="apps">'.$spanOverlay;
		$output .= '<a class="Bild" href="'.$AffLink.'" target="_blank">';
		$output .= '<img class="Image" align="left" src="'.$cacheimageurl.'" alt="'.$trackName.' (AppStore Link) " /></a> ';
		$output .= '<span class="Titel">'.$trackName.'</span><br /> ';
		$output .= '<span class="Hersteller">'.$langDeveloper.' </span> <a href="'.$sellerUrl.'" target="_blank">'.$sellerName.'</a><br /> ';
		$output .= '<div><span class="Freigabe">'.$langAgeRating.' </span>'.$contentAdvisoryRating.$ratingHTML.'</div> ';
		$output .= '<span class="Preis">'.$langPrice.' </span>'.$formattedPrice.' ';	
		if (APPSTORE_DL_LINKNAME != '') {
			$output .= '<span class="Download">'.$ThickBoxLink.APPSTORE_DL_LINKNAME.'</a></span>';	
		} else {
			if ($isMacSoftware == 1) {
				$badgeURL =	 WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY."/images/Mac_App_Store_small.gif";
			} else {
				$badgeURL = WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY."/images/App_Store_small.gif";
			}		
			$output .= $ThickBoxLink.'<img src='.$badgeURL.' style="padding-top: 5px;padding-left: 5px;position: absolute;"></a>';
		}
		
		$output .= '</span><div style="clear: both"></div>';
	}
	
	$screenshots = "";
 	if ($type >= 1 /* && $obj->results[0]->screenshotUrls[0] <> '' */ ) {
		$screenshots .= "<div class='appImageContainer'><div class='appSliderGallery'><ul> ";
			
			//schleife für bilder hier einfügen
			for ($y = 0; $y < 5; $y++) {
				$url = $obj->results[0]->screenshotUrls[$y];
				if ($url=="") {
					break;
				}
				if ($isMacSoftware==0) {
					$imageformats = array(".png", ".tiff",".tif");	
					$urlthump = str_replace($imageformats,'.320x480-75.jpg', $url);		
				} else {
					$imageformats = array(".png", ".tiff",".tif");	
					$urlthump = $url; // Temporary fix
					//$urlthump = str_replace($imageformats,'.800x500-75.jpg', $url);	
				} 		
				
				$screenshots .= '<li><a href="'.$url.'"><img class="appScreenshot" src="'.$urlthump.'"</img></a></li>';	
			}
			
			for ($y = 0; $y < 5; $y++) {
				$url = $obj->results[0]->ipadScreenshotUrls[$y];
				if ($url=="") {
					break;
				}				
				$urlthump = str_replace('1024x1024-65','480x480-75', $url);
				$screenshots .= '<li><a href="'.$url.'"><img class="appScreenshot" src="'.$urlthump.'"</img></a></li>';	
			}
		$screenshots .= '</ul></div></div> ';
	}	
	if ($type == 1 && $isMacSoftware == 0 ) {
		return ('<div class="appBundle">'.$output.$screenshots."</div>");
	} else {
		return ($output.$screenshots);
	}	
	
}

if ( $_REQUEST['appid'] != "" ) {
	header("Location: ".AppStore_getAff($_REQUEST['appid']));
 }

if ( $_REQUEST['appsearch'] != "" ) {
	$result = file_get_contents(APPSTORESEARCHLINKNAME.urlencode($_REQUEST['appsearch']));	
	
	// Decode Content	
	$obj = json_decode($result);
	
	$count = $obj->resultCount;
	$searchresult = "" ; 
	
	if ($count > 0){
		for ( $i = 0; $i < $count; $i++) {
			$afflink = AppStore_CreateLink($obj->results[$i]->trackViewUrl);
			$imageUrl = $obj->results[$i]->artworkUrl60;
			$trackName = $obj->results[$i]->trackName;
			// Lange Titel kürzen
			if (strlen($trackName)>=40) {
				$trackName = left($obj->results[$i]->trackName,37)."..." ;
			}
			//$searchresult .= '<li>';
			$searchresult .= '<a href="'.$afflink.'" target="_blank">';
			$searchresult .= '<img align="left" style="margin-right: 5px; margin-left: 10px; margin-top: 0px; margin-bottom: 10px;" ';
			$searchresult .= 'src="'.$imageUrl.'" alt="'.$trackName.' (AppStore Link) " width="20" height="20" /></a> ';
			$searchresult .= "<a href='".$afflink."'>".$trackName."</a><br /><br />";
			//$searchresult .=  
		}
		//$searchresult .= "</ ol>";
		echo $searchresult;
	}
 } 

 if ( $_REQUEST['searchDetail'] != "" ) {
	$result = file_get_contents(APPSTORESEARCHLINKNAME.urlencode($_REQUEST['searchDetail']));	
	
	// Decode Content
	$obj = json_decode($result);
	
	$count = $obj->resultCount;
	$searchresult = '<center><span style="font-weight:bold;">Deine Suche hat '.$count.' Resultate erzielt</span></center><br />' ; 
	
	if ($count > 0){
		for ( $i = 0; $i < $count; $i++) {
			$afflink = AppStore_CreateLink($obj->results[$i]->trackViewUrl);
			$imageUrl = $obj->results[$i]->artworkUrl60;
			$trackName = $obj->results[$i]->trackName;
			$description = left($obj->results[$i]->description,400)."...";
			$trackName = $obj->results[$i]->trackName;
			$contentAdvisoryRating = $obj->results[$i]->contentAdvisoryRating;
			$sellerName = $obj->results[$i]->sellerName;
			$sellerUrl = $obj->results[$i]->sellerUrl;
			$price = $obj->results[$i]->price;
			$AppStore_country = get_option("AppStore_country");
			$currency = $obj->results[0]->currency;
			$formattedPrice = $obj->results[0]->formattedPrice;			
			
			$searchresult .= '<table valign="top"><tr><td width=60px>';
			$searchresult .= '<a href="'.$afflink.'" target="_blank">';
			$searchresult .= '<img align="right" style="margin-right: 5px; margin-left: 10px; margin-top: 5px; margin-bottom: 10px; -webkit-border-radius: 10px;" ';
			$searchresult .= 'src="'.$imageUrl.'" alt="'.$trackName.' (AppStore Link) " width="57px" height="57px" border="0px" /></a> </td>';
			//$searchresult .= "<a href='".$afflink."'>".$trackName."</a><br />";
			
			$searchresult .= '<td width=30%><span style="font-size: 100%; font-weight:bold; ">'.$trackName.' </span><br /> ';
			$searchresult .= '<span  style="font-size: 80%; font-weight:bold; ">Hersteller: </span><a href="'.$sellerUrl.'" target="_blank">'.$sellerName.'</a><br /> ';
			$searchresult .= '<span  style="font-size: 80%; font-weight:bold;">Freigabe: </span>'.$contentAdvisoryRating.'<br /> ';
			$searchresult .= '<span  style="font-size: 80%; font-weight:bold;">Preis: </span>'.$formattedPrice.' ';
			$searchresult .= '<span  style="font-size: 100%; font-weight:bold; "><a href="'.$afflink.'" target="_blank">Download via iTunes</a></span><br /> </td>';
			$searchresult .= '<td width=60%><span  style="font-size: 80%; font-weight:bold;">Beschreibung:</span><br />'.$description.' </td> ';
			$searchresult .= '</tr></table><br /><br /> ';
		}
	}
	echo $searchresult;
 } 
 


function AppStoreLinks_activate()
{
	// ToDo: Testen ob Jason installiert ist!
	add_option("AppStore_country","at");
	add_option("AppStore_language","de_de");
	add_option("AppStore_picCache","12");
	add_option("AppStore_dataCache","6");
	add_option("AppStore_dlLinkname","Download (Aff.Link)");
	add_option("AppStore_tdlink","2157876");
	add_option("AppStore_Loop",0);
	add_option("AppStore_SimpleClickCounter",0);
	add_option("appStore_db_version", "0");
	add_option("AppStore_style",".appImageContainer {width: auto;margin: 0px;}");
	add_option("AppStore_showRatings", "0");
	add_option("AppStore_enableStats", "checked");
	add_option("AppStore_customAffURL","");
	
	// Datenbank installieren
	global $appStore_db_version;
	$appStore_db_version = "0.6";
	global $wpdb;
	$tablename = $wpdb->prefix.APPSTORE_TABLENAME;
	if(($wpdb->get_var("show tables like '$tablename'") != $tablename) || ($appStore_db_version <> get_option("appStore_db_version"))) {
		  //price NUMERIC(5,2),
		  //currency char(3) DEFAULT '' NOT NULL, 
		  
		  $sql = "CREATE TABLE " . $tablename . " (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  appid bigint(11) DEFAULT '0' NOT NULL,
		  country char(5) DEFAULT '' NOT NULL, 
		  user_agent char(150) DEFAULT '' NOT NULL,
		  ip char(15) DEFAULT '' NOT NULL,
		  createdWhen TIMESTAMP NOT NULL,
		  UNIQUE KEY id (id)
			);";
			
		  require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		  dbDelta($sql);
		  update_option("appStore_db_version", $appStore_db_version );
	}
	
	// check cache Directory
	 if (!file_exists(APPSTORE_CONTENT_DIR)){
        mkdir(APPSTORE_CONTENT_DIR) or error_message('Das Cache Verzeichnis konnte nicht angelegt werden'); // Legt das Cache Verzeichnis an. Sollte dies nicht möglich sein, so wird ein Fehler ausgegeben        
    }
	chmod(APPSTORE_CONTENT_DIR, 0777); // Gibt dem Cache Verzeichniss die nötigen Schreib- und Lese Rechte
	
	 // Check allow_url_fopen
	if(allow_url_fopen=='off') 
	{
		echo'"allow_url_fopen" ist auf diesem Server deaktiviert, wird aber ben&ouml;tigt.<br />';
		echo'Bitte aktivieren sie diese Funktion (setzten sie sich ggf. mit ihrem Hoster in verbindung. '; 
	}  

}

function WPWall_ScriptsAction()
{
 wp_enqueue_script('thickbox');
 }
 
// Nur wenn der User Adminrechte hat ist
if(is_admin()){
    add_action('admin_menu', 'AppStore_options');
}

/////////////////////
// Optionen einbinden
/////////////////////
function AppStore_options() {
		
	add_menu_page('Settings', 'AppStore', 3, basename(__FILE__), 'AppStore_options_page', WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY.'/button/mcebutton.png');
	
	if (get_option("AppStore_enableStats")=="checked") {
		add_submenu_page( basename(__FILE__), 'Statistik', 'Statistik', 3, 'AppStoreStatistik', 'AppStoreshowStatistik');
	}
}

function  AppStore_options_page() {
	$AdminPage = null;
	
	if(isset($_POST['AppStore_save_options']))
		{
			update_option("AppStore_country",attribute_escape($_POST['AppStore_country']));
			update_option("AppStore_language",attribute_escape($_POST['AppStore_language']));
			update_option("AppStore_picCache",attribute_escape($_POST['AppStore_picCache']));
			update_option("AppStore_dataCache",attribute_escape($_POST['AppStore_dataCache']));
			update_option("AppStore_dlLinkname",attribute_escape($_POST['AppStore_dlLinkname']));
			update_option("AppStore_tdlink",attribute_escape($_POST['AppStore_tdlink']));
			update_option("AppStore_style",attribute_escape($_POST['AppStore_style']));
			update_option("AppStore_showRatings",attribute_escape($_POST['AppStore_showRatings']));
			update_option("AppStore_enableStats",attribute_escape($_POST['AppStore_enableStats']));
			update_option("AppStore_customAffURL",attribute_escape($_POST['AppStore_customAffURL']));
			update_option("AppStore_PHGToken",attribute_escape($_POST['AppStore_PHGToken']));
		}
		
		$Countries = getCountries();
							
		asort($Countries);

		$language = array(
			"de_de" => __("Deutsch", 'appstore'),
			"es_es" => __("Spanisch", 'appstore'),
			"it_it" => __("Italienisch", 'appstore'),
			"pl_pl" => __("Polnisch", 'appstore'),
			"zh_cn" => __("Chinesisch", 'appstore'),
			"de_de" => __("Deutsch", 'appstore'),
			"en_us" => __("Englisch", 'appstore')
			);
		asort($language);	
		
		$AdminPage .= "
		
		<div class=\"appStore-sidebox\">
			<strong>Donate</strong>
			<p>If you like the Appstore Plugin, you can support its development by a donation:</p>
			<hr>
			<div>
				<script type=\"text/javascript\">
				/* <![CDATA[ */
				    (function() {
				        var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
				        s.type = 'text/javascript';
				        s.async = true;
				        s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
				        t.parentNode.insertBefore(s, t);
				    })();
				/* ]]> */
				</script>				
				<a class=\"FlattrButton\" style=\"display:none;\"
				href=\"http://tirolercast.ste-bi.net/wordpress-plugins/appstore-plugin/\">
				</a>
			<hr>
				<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
				<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
				<input type=\"hidden\" name=\"hosted_button_id\" value=\"9401356\">
				<input type=\"image\" src=\"https://www.paypal.com/de_DE/AT/i/btn/btn_donateCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"Jetzt einfach, schnell und sicher online bezahlen ֠mit PayPal.\">
				<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/de_DE/i/scr/pixel.gif\" width=\"1\" height=\"1\">
				</form>
			</div>
			<hr>
			<div>
				<a href=\"http://amzn.to/Steeeve\">My Amazon.de wishlist</a>
			</div>
			<hr>
			<strong>News</strong>
			<div>			
				<a class='twitter-timeline' href='https://twitter.com/AppStore_plugin' data-widget-id='438657927096705024'>Tweets of @AppStore_plugin</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>
			</div>
		</div>
		
		<form method=\"post\" action=\"".get_settings("siteurl")."/wp-admin/options-general.php?page=AppStore.php\">
			<div class=\"wrap\">
			<div class=\"icon32\" id=\"icon-options-general\"><br/></div>
			
			<h2>".__("AppStore-Plugin Settings", 'appstore')."</h2>
			
			
			<p>".__("This Plugin is easyly to use with <code>[app 123456]</code>.", 'appstore')."
			</p>
			
			<table>
			<tr>
				<td>".__("iTunes-Store Country:", 'appstore')."<a href='#country'>(?)</a></td>
				<td>
					<select name=\"AppStore_country\" style=\"width:400px;font-weight:bold;\">";
						
						foreach($Countries as $Key => $Country)
						{
							$AdminPage .= "<option value=\"".strtolower($Key)."\"";
							if(strtolower(get_option("AppStore_country")) == strtolower($Key)) $AdminPage .= " selected=\"selected\"";
							$AdminPage .= ">".$Country."</option>";
						}
						
					$AdminPage .= "</select>
				</td>
			</tr>
			<tr>
				<td>".__("Language:", 'appstore')."<a href='#language'>(?)</a></td>
				<td>
					<select name=\"AppStore_language\" style=\"width:400px;font-weight:bold;\">";
						
						foreach($language as $Key => $language)
						{
							$AdminPage .= "<option value=\"".$Key."\"";
							if(get_option("AppStore_language") == $Key) $AdminPage .= " selected=\"selected\"";
							$AdminPage .= ">".$language."</option>";
						}
						
					$AdminPage .= "</select>
				</td>				
			</tr>
			<tr>		
					<td>".__("Cachingtime pictures (in h):", 'appstore')."<a href='#caching'>(?)</a></td>
					<td><input type=\"text\" name=\"AppStore_picCache\" value=\"".get_option("AppStore_picCache")."\" style=\"width:400px;font-weight:bold;font-size:9pt;height:20px;padding:1px;border:1px solid #DDDDDD;\"/></td>
			</tr>
			
			
			<tr>
					<td>".__("Cachingtime data (in h):", 'appstore')."<a href='#caching'>(?)</a></td>
					<td><input type=\"text\" name=\"AppStore_dataCache\" value=\"".get_option("AppStore_dataCache")."\" style=\"width:400px;font-weight:bold;font-size:9pt;height:20px;padding:1px;border:1px solid #DDDDDD;\"/></td>
			</tr>
			<tr>
					<td>".__("Downloadlink name:", 'appstore').'<br />'.__(" (set empty to display logo)",'appstore')."</td>
					<td><input type=\"text\" name=\"AppStore_dlLinkname\" value=\"".get_option("AppStore_dlLinkname")."\" style=\"width:400px;font-weight:bold;font-size:9pt;height:20px;padding:1px;border:1px solid #DDDDDD;\"/></td>
			</tr>			
			<tr>		
				<td>".__("PHG affiliate token :", 'appstore')."<a href='#td'>(?)</a></td>
				<td><input type=\"text\" name=\"AppStore_PHGToken\" value=\"".get_option("AppStore_PHGToken")."\" style=\"width:400px;font-weight:bold;font-size:9pt;height:20px;padding:1px;border:1px solid #DDDDDD;\"/></td>							
			</tr>
			<tr>	
			<tr>		
				<td>".__("Custom affiliate URL:", 'appstore')."<a href='#custom'>(?)</a></td>
				<td><input type=\"text\" name=\"AppStore_customAffURL\" value=\"".get_option("AppStore_customAffURL")."\" style=\"width:400px;font-weight:bold;font-size:9pt;height:20px;padding:1px;border:1px solid #DDDDDD;\"/></td>				
			</tr>
			
			<tr>				
				<td>".__("Activate ratings:", 'appstore')."</td>
				<td><input type=\"checkbox\" name=\"AppStore_showRatings\" value=\"checked\" style=\"width:400px;height:20px;padding:1px;border:1px solid #DDDDDD; allign:center\"/ ".get_option("AppStore_showRatings")."></td>											
			</tr>
			<tr>	
				<td>".__("Activat statistics:", 'appstore')."</td>
				<td><input type=\"checkbox\" name=\"AppStore_enableStats\" value=\"checked\" style=\"width:400px;height:20px;padding:1px;border:1px solid #DDDDDD; allign:center\"/ ".get_option("AppStore_enableStats")."></td>
									
			</tr>			
			<tr>	
				<td>".__("Style (CSS):", 'appstore')."</td>
				<td><textarea rows=\"10\" cols=\"54\" name=\"AppStore_style\" class=\"code\">".get_option("AppStore_style")."</textarea></td>
				
			</tr>
			
				<td></td><td ><input type=\"submit\" name=\"AppStore_save_options\" value=\"Save\" style=\"width:400px;font-weight:bold;font-size:9pt;height:20px;padding:1px;border:1px solid #DDDDDD;\"/></td>
			</tr>
			</table>
		
			</form>
			<a name='use'><h2>".__("Usage and example", 'appstore')."</h2></a>
			".__("You will get the app-id from the iTunes-Store. Simply copy the link and look for the Number near the end of the link. Otherwise you can use <a href='http://appsuche.touchtalk.at'>http://appsuche.touchtalk.at</a> to get an embedcode.", 'appstore')."<br /><br />
			
			http://itunes.apple.com/WebObjects/MZStore.woa/wa/viewSoftware?id=284417350&mt=8 <br /><br />
		
			[app 284417350] ".__("This should insert the Apple remote app in your post.", 'appstore')." <br />
			[appimg 284417350] ".__("only shows the screenshot.", 'appstore')."<br />
			[appext 284417350] ".__("more extendet view.", 'appstore')."<br /><br />
			
			
			
			<a name='info'><h2>".__("Info", 'appstore')."</h2></a>
			<a name='country'><h3>".__("Country", 'appstore')."</h3></a>
			".__("The country where the app is loaded (because not every app is in every store)", 'appstore')."
			
			<a name='language'><h3>".__("Language", 'appstore')."</h3></a>
			".__("If you change the language or the country, maybe deleting the cache is a good choice", 'appstore')."
			
			<a name='caching'><h3>".__("Cachingtime", 'appstore')."</h3></a>
			".__("How often the data is loadet from the server", 'appstore')."
			
			<a name='td'><h3>".__("PHG-Token", 'appstore')."</h3></a>
			".__("To earn money ;-)", 'appstore')."
				
			<a name='custom'><h3>".__("Custom Affiliate URL:", 'appstore')."</h3></a>
			".__("To use alternate affiliate programs.<br />
			zB: http://click.linksynergy.com/fs-bin/stat?id=AAAAAAAA&offerid=100000&type=3&subid=0&tmpid=0006&RD_PARM1=<b>{URL}</b>&partnerId=99
			
			", 'appstore')." 
						
			<h2>".__("Unterst&uuml;tzung", 'appstore')."</h2>
			".__("As you know, developer do not really earn a lot of money. Sad, but true. Thats the reason every Du wei&szlig;t ja, ein Entwickler ist prinzipiell arm. Ja,. click (not sale) goes to our PGH-ID. So please do not remove this code. Thanks!", 'appstore');

	echo $AdminPage;
}


function AppStoreLinks_SetSyle() {
		echo '<link href="'.WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY.'/css/AppStore.css" rel="stylesheet" type="text/css"/>';
		$individualStyle = get_option("AppStore_style");
		if ($individualStyle <> "") {
			echo "<style type='text/css'>".$individualStyle."</style>";
		}
}


function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/AppStore.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}

 
function appstore_add_button($buttons)
{
    array_push($buttons, "separator", "appstoreButton");
    return $buttons;
}

function appstore_register($plugin_array)
{
	$siteurl = get_option('siteurl');
	$url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/button/editor_plugin.js';
   
    $plugin_array['appstoreButton'] = $url;
    return $plugin_array;
}
 
add_filter('mce_external_plugins', "appstore_register");
add_filter('mce_buttons', 'appstore_add_button', 0);

add_action('wp_print_scripts', 'WPWall_ScriptsAction');
add_action('wp_head', 'AppStoreLinks_SetSyle');
add_action('admin_head', 'admin_register_head');

add_shortcode('app', 'appstore_process');
add_shortcode('appext', 'appstore_process_ext');
add_shortcode('appimg', 'appstore_process_img');

add_filter('comment_text', 'do_shortcode');
add_filter('the_content_rss', 'do_shortcode');

load_plugin_textdomain('appstore',null, dirname(plugin_basename(__FILE__))."/languages/");

register_activation_hook(__FILE__, 'AppStoreLinks_activate');
?>