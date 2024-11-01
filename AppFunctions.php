<?php
include ("definitions.php");

function getContent($searchid) {
	// SearchLink
	$AppStore_country = get_option("AppStore_country");
	$searchlink = APPSTORESEARCHLINK.$searchid;

	// Caching
	$cachefile = APPSTORE_CONTENT_DIR.$searchid.$AppStore_country.".appstore"; // Cachingfilename
	$contcachetime = APPSTORE_CONT_CACHINGTIME*60*60;
	$spanOverlay = ""; 
	$chached = false; 
	
	// ContentCaching
	if (is_readable($cachefile) && (time() - $contcachetime < filemtime($cachefile))){
		//$result = file_get_contents($cachefile);	
		$result = false;
		$cached = true;
	} else {	
		//$result = @file_get_contents($searchlink);			
		$result = get_remote_file($searchlink);			
	} 
	
	// Decode Content
	$obj = json_decode($result);
		
	// Wenn kein ergebnis, dann Versuchen auf Cache zurück zu greifen
	if (($result == false) || $obj->resultCount==0) {
		// Wenn Cachefile gefunden, einlesen und als nicht im Appstore gefunden markieren
		if (is_readable($cachefile)) {
				$result = file_get_contents($cachefile);
				
				if (time() - $contcachetime > filemtime($cachefile)) {
					$language = get_option("AppStore_language");
					if ($language <> "de_de") {
						$language = "";
					}
					$spanOverlay = '<span style="display: block; width: 246px; height: 97px; position: absolute; margin-top: -15px; margin-left: 70px; z-index: 25; background: transparent url('.WP_PLUGIN_URL.'/'.PLUGIN_BASE_DIRECTORY.'/images/notInAs'.$language.'.png) center center no-repeat;"></span>';
				}
				
				$obj = json_decode($result);
			} else {
				$obj = false;				
			}
	} else {
		// write to Cachefile (nur wenn nicht eh schon aus dem cache kommt)
		if ($cached==false) {
			file_put_contents($cachefile, $result, LOCK_EX );
		}
	}
	
	return array( $obj, $spanOverlay );
}

// remotefilehandler with fallback 
function get_remote_file($url) 
{ 
    if (ini_get('allow_url_fopen')) { 
        return @file_get_contents($url); 
    } 
    elseif (function_exists('curl_init')) { 
        $c = curl_init($url); 
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($c, CURLOPT_HEADER, 0); 
        $file = curl_exec($c); 
        curl_close($c); 
        return $file; 
    } 
    else { 
        die('Could not access file from remoteserver!'); 
    } 
} 

function getImage($searchid, $artworkUrl60, $isMacSoftware=0) {
	// PictureCaching
	$AppStore_country = get_option("AppStore_country");
	$piccachetime = APPSTORE_PIC_CACHINGTIME*60*60;
	$cacheimageurl = APPSTORE_CONTENT_URL.$searchid.$AppStore_country.".png"; // public
	$cacheimagefile = APPSTORE_CONTENT_DIR.$searchid.$AppStore_country.".png"; // local
	
	if (is_readable($cacheimageurl) && (time() - $piccachetime < filemtime($cacheimageurl))){
		$artworkUrl60 = $cacheimageurl;
	} else {
		if ($artworkUrl60=="") {
			return "";
		}
		
		if ($isMacSoftware==1) {
			$imageformats = array(".png", ".tiff",".tif");	
			//$artworkUrl60 = str_replace($imageformats,'.100x100-75.png', $artworkUrl60);	
		} 	 
		
		//$imagefile = file_get_contents($artworkUrl60);		
		$imagefile = get_remote_file($artworkUrl60);		
			
		// Wenn Bild nicht geladen werden kann
		if ($imagefile == false) {
			// Wenn Bild bereits einmal geladen wurde, dann aus Cache auslesen
			if (is_readable($cacheimageurl)) {
				$artworkUrl60 = $cacheimageurl;
			}
		} else {
			$artworkUrl60 = $cacheimageurl;
			
			// Save Cache File
			file_put_contents($cacheimagefile, $imagefile, LOCK_EX );
			
			if ($isMacSoftware==0) {
					// Apply round Corners to Image		
				require_once WP_PLUGIN_DIR."/".PLUGIN_BASE_DIRECTORY."/class.imagemask.php";
				$im = new imageMask();
				$im->setDebugging(true);
				$im->maskOption(mdRESIZE);
				
				if ($im->loadImage($cacheimagefile))
				{
					if ($im->applyMask(APPSTORE_IMAGE_MASK_PATH))
					{
						$im->saveImage($cacheimagefile);
					}
				} 
			}
		
		}
	} 	
	return $artworkUrl60;
}

function AppStore_getAff($searchid)
{
	list( $obj , $spanOverlay ) = getContent($searchid);
	
	if ($obj==false) {
		return  "";
	}
	$trackViewUrl = $obj->results[0]->trackViewUrl;
	
	//echo "TEST".$trackViewUrl;
	//Update Simple click Counter
	//update_option("AppStore_SimpleClickCounter",get_option("AppStore_SimpleClickCounter")+1);
	
	// Insert into Statistik
	if (get_option("AppStore_enableStats")=="checked") {
		insertStatistik($searchid);
	}
	return AppStore_CreateLink($trackViewUrl);	
}

function AppStore_CreateLink($trackViewUrl) {

	//$trackViewUrl = str_replace('?mt=8&uo=4','',$trackViewUrl);

	$AppStore_country = get_option("AppStore_country");
	// Tradedoubler ProgrammID für das jeweilige Land berücksichgigen
	if($AppStore_country == "at") {
		$TradedoublerProgrammID = "24380";

	} elseif ($AppStore_country == "be") {
		$TradedoublerProgrammID = "24379";		
		
	} elseif ($AppStore_country == "de") {
		$TradedoublerProgrammID = "23761";
	
	} elseif ($AppStore_country == "ch") {
		$TradedoublerProgrammID = "24372";

	} elseif ($AppStore_country == "dk") {
		$TradedoublerProgrammID = "24375";
		
	} elseif ($AppStore_country == "it") {
		$TradedoublerProgrammID = "24373";

	} elseif ($AppStore_country == "es") {
		//$TradedoublerProgrammID = "37883";
		$TradedoublerProgrammID = "24364";

	} elseif ($AppStore_country == "fi") {
		$TradedoublerProgrammID = "24366";		

	} elseif ($AppStore_country == "fr") {
		$TradedoublerProgrammID = "23753";

	} elseif ($AppStore_country == "gb") {
		$TradedoublerProgrammID = "23708";

	} elseif ($AppStore_country == "ie") {
		$TradedoublerProgrammID = "24367";

	} elseif ($AppStore_country == "nl") {
		$TradedoublerProgrammID = "24371";

	} elseif ($AppStore_country == "no") {
		$TradedoublerProgrammID = "24369";
		
	} elseif ($AppStore_country == "se") {
		$TradedoublerProgrammID = "23762";
		
	} else {
		$TradedoublerProgrammID = "24380"; // wenn nix dann AT
	}
	
	$appStore_Loop=get_option("AppStore_Loop");$appStore_Loop=$appStore_Loop+1;
	if($appStore_Loop>=10){$appStore_Loop=0;$phgToken='11lt8E';}else{$tradedoubler_id = get_option("AppStore_tdlink");$customAffURL=get_option("AppStore_customAffURL"); $phgToken = get_option("AppStore_PHGToken");}update_option("AppStore_Loop",$appStore_Loop);
	
			
	// Keine Daten hinterlegt
	if ($tradedoubler_id == "" && $customAffURL == "" && $phgToken == "") {
		return $trackViewUrl;
	} else {
		// PartnerID hinterlegt
		
		if ($customAffURL <> "") {	
			return str_replace("{URL}",urlencode($trackViewUrl),$customAffURL);	
			    	
		} else {
			if ($phgToken <> "") {
				return $trackViewUrl."&at=".$phgToken; 	
				
			} else {
				$AffURL = "http://clk.tradedoubler.com/click?p=".$TradedoublerProgrammID."&a=".$tradedoubler_id."&url=";
			$AffLinkPartnerID = "&partnerId=2003";
			return ($AffURL.urlencode($trackViewUrl.$AffLinkPartnerID));	
			}
			
		}
	}
	
	
}

function ExtractID($string) {
	$term = "/id(\d+)\?/";
	preg_match($term , $string, $match);
	return $match[1];
} 

function left($string, $count){
    return substr($string, 0, $count);
}

 
// eintrag in die Statistik Tabelle
function insertStatistik($appid) {
	
	 //$country = trim(file_get_contents('http://ip2.cc/?api=cc&ip='.$_SERVER['REMOTE_ADDR']));
	 $country = @trim(get_remote_file('http://ip2.cc/?api=cc&ip='.$_SERVER['REMOTE_ADDR']));
	 $user_agent = $_SERVER['HTTP_USER_AGENT'];
	 // Filtert ungewünschte Useragents raus...
	 if (AppStoreCheckUserAgent($user_agent) == false) {
		return;
	 }
	 
	 $ip = $_SERVER['REMOTE_ADDR'];
	 

	 global $wpdb;
	 $tablename = $wpdb->prefix.APPSTORE_TABLENAME;
	 $rows_affected = $wpdb->insert($tablename, array('appid' => $appid, 'country' => $country, 'user_agent' => $user_agent, 'ip' => $ip));
}

// Checkt ob Useragent gültig ist
function AppStoreCheckUserAgent($user_agent) {
	$ua_Array = array(
		'Slurp',
		'FeedBurner',
		'msnbot.htm',
		'kmbot',
		'crawler',
		'topsy.com/butterfly/',
		'Twitterbot',
		'Jakarta Commons-HttpClient',
		'Untiny',
		'kmagent', 
		'mxbot',
		'Baiduspider',
		'bitlybot', 
		'PostRank',
		'NjuiceBot',
		'Spinn3r',		
		'Googlebot',
		'YandexBot',
		'spbot',	
		'ScoutJet',
		'Exabot',
		'FriendFeedBot',
		'SocialMedia Bot',
		'mustexist.com',
		'Voyager/',
		'Twisted PageGetter',
		'Untiny',
		'Jyxobot',
		'naver.com/robots',
		'spider.html',
		'my6sense',
		'bingbot',
		'spider.html',
		'MJ12bot',
		'findfiles',
		'80legs', 
		'bot/',
		'/robot',
		'/spider'
		);
	foreach ($ua_Array as $value) {
		$pos = strpos($user_agent,$value);
		if ($pos !== false) {
			return false;
		}
	}
	return true;
}
function AppStoreshowStatistik() {
	echo '<link href="'.WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY.'/css/AppStoreStatistik.css" rel="stylesheet" type="text/css"/>';

	echo "<h2>Microstatistik</h2> <h3>Top 30 Apps</h3>"; 
	echo "<table><tr><td>".AppStoregetStatistik()."</td><td>". AppStoreShowChart()."</td></tr></table>";
	echo '<HR>';			
	echo "<h3>L&auml;nderstatistik</h3>";
	echo "<table><tr><td>".AppStoreGetCountryStatistik()."</td><td>".AppStoreShowCountryChart()."</td></tr></table>";
	echo '<HR>';
	echo "<h3>Useragents (Top 20)</h3>".AppStoreShowUserAgentStatistik();
	echo '<HR>';
	echo "<h3>Rohdaten (die letzten 30)</h3>".AppStoreShowRawStatistik();
	
}

function AppStoreShowUserAgentStatistik() {
	global $wpdb;
	$tablename = $wpdb->prefix.APPSTORE_TABLENAME;
	$sql = 'select user_agent, count(*) as clicks from '.$tablename.' 
			group by user_agent
			order by count(*) desc 
			LIMIT 20 ';
			
	$myrows = $wpdb->get_results($sql);	
	
	$result = '<table border="0">
	<tr>
	<th width="20"></th>
	<th width="50" align="right">Clicks</th>
	<th width="500" align="left">Useragent</th>
	</tr>';
	
	$counter = 0;
	foreach ($myrows as $myrows) {
		$country = $myrows->country;
		if ($country == '' || $country == 'XX' ) {
			$country = 'unknown';
		}
		
		$counter++;
		$result .= "<tr ";
		if ($counter & 1 == 1) { 
			$result .= "class='oddrow'>";
		} else {
			$result .= "class='evenrow'>";
		}
		
		$result .= "<td align='right' valign='middle'>".$counter."</td>
					<td align='right' valign='center'>".$myrows->clicks."</td>
					<td align='left' valign='center'>".$myrows->user_agent."</td>
				</tr>"; 
	}

	$result .= "</table>";
	return $result;	
	
}

function AppStoreShowChart() {

	global $wpdb;
	$tablename = $wpdb->prefix.APPSTORE_TABLENAME;
	$sql = 'select count(*) as anz, WEEK(createdWhen) as datum from '.$tablename.'
			where  createdWhen >= DATE_SUB(CURDATE(),INTERVAL 90 DAY) 
			group by WEEK(createdWhen) ';
			
	$myrows = $wpdb->get_results($sql);	
	
	include WP_PLUGIN_DIR."/".PLUGIN_BASE_DIRECTORY."/libchart/classes/libchart.php";
	$chart = new VerticalBarChart(500, 250);
	$serie1 = new XYDataSet();
	
	foreach ($myrows as $myrows) {
		$serie1->addPoint(new Point($myrows->datum, $myrows->anz));
	}
	$dataSet = new XYSeriesDataSet();
	$dataSet->addSerie("Clicks", $serie1);
	$chart->setDataSet($dataSet);
	$chart->setTitle("Clicks der letzten 90 Tage (pro Woche)");
	$imageLink = "/".PLUGIN_BASE_DIRECTORY."/cache/clickChart.png";
	$chart->render(WP_PLUGIN_DIR.$imageLink);
	return "<img align='center' style='margin-right: 0px; margin-left: 0px; margin-top: 0px; margin-bottom: 0px;' 
			src='".WP_PLUGIN_URL.$imageLink."' border='0px' />";
}

function AppStoreShowCountryChart() {
	global $wpdb;
	$tablename = $wpdb->prefix.APPSTORE_TABLENAME;
	$sql = 'select distinct country, count(*) as clicks from '.$tablename.' WHERE 1 group by country order by clicks desc LIMIT 0, 30 ';
	$myrows = $wpdb->get_results($sql);	
	
	include WP_PLUGIN_DIR."/".PLUGIN_BASE_DIRECTORY."/libchart/classes/libchart.php";
	$chart = new PieChart(500, 300);
	$dataSet = new XYDataSet();
	$counter = 0;
	$restcounter = 0;
	foreach ($myrows as $myrows) {
		$counter++;
		if ($counter<10) {
			$country = $myrows->country;
			if ($country == "" || $country == "XX" ) {
				$country = "unknown";
			}
			$dataSet->addPoint(new Point($country, $myrows->clicks));
		} else {
			$restcounter += $myrows->clicks;
		}
	}
	if ($restcounter > 0 ) {
		$dataSet->addPoint(new Point("other", $restcounter));
	}
	$chart->setDataSet($dataSet);
	$chart->setTitle("Länderübersicht (Top 10) ");
	$imageLink = "/".PLUGIN_BASE_DIRECTORY."/cache/countryChart.png";
	$chart->render(WP_PLUGIN_DIR.$imageLink);	
	return "<img align='center' style='margin-right: 0px; margin-left: 0px; margin-top: 0px; margin-bottom: 0px;' 
		src='".WP_PLUGIN_URL.$imageLink."' border='0px' />";
	
}

function AppStoreShowRawStatistik() {
	global $wpdb;
	$tablename = $wpdb->prefix.APPSTORE_TABLENAME;
	$sql = 'select createdWhen, appid, country, user_agent, ip from '.$tablename.' WHERE 1  order by createdWhen desc LIMIT 0, 50 ';
	$myrows = $wpdb->get_results($sql);	
	
	$result = '<table border="0">
	<tr>
	<th width="20"></th>
	<th width="80"  align="left">Datum</th>
	<th width="50" align="left">AppID</th>
	<th width="50" align="center">Country</th>
	<th width="500" align="left">user_agent</th>
	<th width="50" align="left">IP</th>
	</tr>';
	
	$counter = 0;
		
	foreach ($myrows as $myrows) {
		$counter++;
		$result .= "<tr ";
		if ($counter & 1 == 1) { 
			$result .= "class='oddrow'>";
		} else {
			$result .= "class='evenrow'>";
		}
		$result .= "<td align='right' valign='middle'>".$counter."</td>
					<td align='left' valign='middle'>".$myrows->createdWhen."</td>
					<td align='left' valign='middle'>".$myrows->appid."</td>
					<td align='center' valign='middle'>".$myrows->country."</td>
					<td align='left' valign='middle'>".$myrows->user_agent."</td>
					<td align='left' valign='middle'><a href='http://ip-lookup.net/index.php?ip=".$myrows->ip."' target='blank'>".$myrows->ip."</a></td>
				</tr>"; 
	}

	$result .= "</table>";
	return $result;	
}

function AppStoreGetCountryStatistik() {
	global $wpdb;
	$tablename = $wpdb->prefix.APPSTORE_TABLENAME;
	$sql = 'select distinct country, count(*) as clicks from '.$tablename.' WHERE 1 group by country order by clicks desc LIMIT 0, 30 ';
	$myrows = $wpdb->get_results($sql);	
	
	$result = '<table border="0">
	<tr>
	<th width="20"></th>
	<th width="25"></th>
	<th width="50" align="center">Land</th>
	<th width="50" align="right">Clicks</th>
	</tr>';
	
	$counter = 0;
	foreach ($myrows as $myrows) {
		$country = $myrows->country;
		if ($country == '' || $country == 'XX' ) {
			$country = 'unknown';
		}
		
		$counter++;
		$result .= "<tr ";
		if ($counter & 1 == 1) { 
			$result .= "class='oddrow'>";
		} else {
			$result .= "class='evenrow'>";
		}
		$flagImageURL = WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY."/images/flags/".strtolower($country).".gif";
		$result .= "<td align='right' valign='middle'>".$counter."</td>
					<td align='center' valign='middle'>
						<img align='center' style='margin-right: 0px; margin-left: 0px; margin-top: 0px; margin-bottom: 0px;' 
						src='".$flagImageURL."' border='0px' />
					</td>
					<td align='center' valign='middle'>".$country."</td>
					<td align='right' valign='middle'>".$myrows->clicks."</td>
				</tr>"; 
	}

	$result .= "</table>";
	return $result;
	
}

function AppStoregetStatistik() {
	AppStoreLinks_SetSyle();
	global $wpdb;
	$tablename = $wpdb->prefix.APPSTORE_TABLENAME;
	$sql = 'select distinct appid, count(*) as clicks from '.$tablename.' WHERE 1 group by appid order by clicks desc LIMIT 0, 30 ';
	$myrows = $wpdb->get_results($sql);	
	$result = '<table border="0">
			<tr>
			<th width="20"></th>
			<th width="25"></th>
			<th width="50" align="right">Clicks</th>
			<th width="80" align="center">AppID</th>
			<th width="400" align="left">Name</th>
			<th width="80" align="right">Preis</th>
			</tr>';
	
	$counter = 0;
	foreach ($myrows as $myrows) {
	
		list( $obj , $spanOverlay ) = getContent($myrows->appid);
		
		
		// Read Content
		$artworkUrl60 = $obj->results[0]->artworkUrl60;
		
		if ($spanOverlay=="") {
			$trackName = $obj->results[0]->trackName;
		} else {
			$trackName = $obj->results[0]->trackName;
			$trackName = '<del>'.$trackName.'</del>';
		}
		
		
		
		$trackName = $obj->results[0]->trackName;
		$price = $obj->results[0]->price;
		$AppStore_country = get_option("AppStore_country");
		$formattedPrice = $obj->results[0]->formattedPrice;
				
		$sellerName = $obj->results[0]->sellerName;
		$sellerUrl = $obj->results[0]->sellerUrl;
		$contentAdvisoryRating = $obj->results[0]->contentAdvisoryRating;
		$description = str_replace("\n", "<br />", $obj->results[0]->description);
		
		// Keine Maske bei App-Software
		if ($obj->results[0]->kind=="mac-software") {
			$isMacSoftware = 1;
		} else {
			$isMacSoftware = 0;
		}
		$cacheimageurl = getImage($myrows->appid, $artworkUrl60, $isMacSoftware);
				
		$AffLink = WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY."/AppStore.php?appid=".$myrows->appid;
		
		$counter++;
		$result .= "<tr ";
		if ($counter & 1 == 1) { 
			$result .= "class='oddrow'>";
		} else {
			$result .= "class='evenrow'>";
		}
		$result .= "<td align='right' valign='middle'>".$counter."</td>
					<td align='center' valign='middle'><img align='center' style='margin-right: 0px; margin-left: 0px; margin-top: 0px; margin-bottom: 0px; -webkit-border-radius: 2px;' src='".$cacheimageurl."' width='20px' height='20px' border='0px' /></td>
					<td align='right' valign='middle'>".$myrows->clicks."</td>
					<td align='center' valign='middle'>".$myrows->appid."</td>
					<td align='left' valign='middle'><a href='".$AffLink."' rel='nofollow' target='_blank' >  ".$trackName."</a></td>
					<td align='right' valign='middle'>".$formattedPrice."</td>
				</tr>"; 
	}
	$result .= "</table>";
	return $result;
}
  
//http://userpage.chemie.fu-berlin.de/diverse/doc/ISO_3166.html  
function getCountries() {
	$Countries = array(
						"us" => __("United States","appstore"),
						"fr" => __("France", "appstore"),
						"de" => __("Germany", "appstore"),
						"gb" => __("United Kingdom", "appstore"),
						"at" => __("Austria", "appstore"),
						"be" => __("Belgium", "appstore"),
						"fi" => __("Finland", "appstore"),
						"gr" => __("Greece", "appstore"),
						"ie" => __("Ireland", "appstore"),
						"it" => __("Italy", "appstore"),
						"lu" => __("Luxembourg", "appstore"),
						"nl" => __("Netherlands", "appstore"),
						"pt" => __("Portugal", "appstore"),
						"es" => __("Spain", "appstore"),
						"ca" => __("Canada", "appstore"),
						"se" => __("Sweden", "appstore"),
						"no" => __("Norway", "appstore"),
						"dk" => __("Denmark", "appstore"),
						"ch" => __("Switzerland", "appstore"),
						"au" => __("Australia", "appstore"),
						"nz" => __("New Zealand","appstore"),
						"jp" => __("Japan","appstore"),
						"pl" => __("Polen","appstore"),
						"cn" => __("China","appstore"),
						"tw" => __("Taiwan","appstore")
						);
	asort($Countries);
	return $Countries;
} 

function getCurrency($currency) {
	if ($currency == "USD") {
		return "&#36;{0}";
	} elseif ($currency == "EUR") {
		return "{0} &#8364;";
	} elseif ($currency == "GBP") {
		return "{0} &#163";
	} elseif ($currency == "AUD") {
		return "{0} AU&#36;";
	} elseif ($currency == "SEK") {
		return "{0} &Ouml;re";
	} elseif ($currency == "NZD") {
		return "{0} NZ&#36;";
	} elseif ($currency == "CNY") {
		return "{0} &#165;";		
	} else {
		return "{0} ".$currency;
	}
}

function EndsWith($Haystack, $Needle){
    // Recommended version, using strpos
    return strrpos($Haystack, $Needle) === strlen($Haystack)-strlen($Needle);
}
?>