<?php

class WP_AppStore_Widget extends WP_Widget {
	function WP_AppStore_Widget() {
		//Konstruktor
		$widget_ops = array('classname' => 'widget_AppStore_Widget', 'description' => 'Zeigt die Top 10 der AppStore Charts' );
		$this->WP_Widget('AppStore_Widget', 'Appstore', $widget_ops);
		
		if ( is_active_widget(false, false, $this->id_base) )
             add_action( 'wp_head', array(&$this, 'recent_widget_style') );
	}
	
	function recent_widget_style() { 			
			echo '<link href="'.WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY.'/css/widget.css" rel="stylesheet" type="text/css" />';			
	}
 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		$wtitle = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Top 10 Apps' ) : $instance['title']);
		$freePaid = apply_filters('widget_title', empty( $instance['freePaid'] ) ? __( 'free' ) : $instance['freePaid']);
		$topCount = apply_filters('widget_title', empty( $instance['topCount'] ) ? __( '10' ) : $instance['topCount']);
		$freePaid = attribute_escape($instance['freePaid']) ;
		$kind = attribute_escape($instance['appKind']) ;
        $Country = attribute_escape($instance['Country']) ;
		
		echo $before_widget .$before_title . $wtitle . $after_title;	
		$language = get_option("AppStore_language");
		$doc = new DOMDocument();
		$feed = "http://itunes.apple.com/".$Country."/rss/".$kind."/limit=".$topCount."/xml";
	
		$feedUpdateIntervall = $instance['feedUpdateIntervall'];
		
		if ($feedUpdateIntervall == "") {
			$feedUpdateIntervall = 60*60*60;
		} else {
			$feedUpdateIntervall = $feedUpdateIntervall*60*60;
		}
				
		if ((time() - $feedUpdateIntervall) < $instance['feedUpdateTime']) {
			$doc = $instance['feedCache'];
		} else {
			//$doc->load($feed);
			@$doc->loadXML(get_remote_file($feed));
			
			$instance['feedCache'] =  $doc;
			$instance['feedUpdateTime'] = time();
		}
					
		$appList = '<ul class="pageitem">'; 
		$count = 0;
		foreach ($doc->getElementsByTagName('entry') as $node) {
			$count = $count + 1;
			$title = htmlspecialchars($node->getElementsByTagName('name')->item(0)->nodeValue); 
			$id	= ExtractID(utf8_encode($node->getElementsByTagName('id')->item(0)->nodeValue));
			$price	= htmlspecialchars($node->getElementsByTagName('price')->item(0)->nodeValue);
			$sellerName	= htmlspecialchars(($node->getElementsByTagName('artist')->item(0)->nodeValue));
			$image = utf8_encode($node->getElementsByTagName('image')->item(0)->nodeValue);
			$afflink = WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY."/AppStore.php?appid=".$id;
					
			$appList .=	'<li class="store">
					<a class="noeffect" href="'.$afflink.'" rel="nofollow" target="_blank">
					<img class="image" style=" width: 45px; height: 45px; -webkit-border-radius: 8px; -moz-border-radius: 8px;" src=\''.$image.'\'></img>
					<span class="comment">'.$sellerName.'</span><span class="name">'.$title.'</span>
					<span class="comment">'.$price.'</span>					
					</a>
				</li>';
			if ($count==$topCount){
				break;
				}
			
			}
		$appList .= "</ul>";
		echo $appList; 
				
		echo $after_widget;
	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['freePaid'] =  strip_tags($new_instance['freePaid']);
		$instance['topCount'] =  strip_tags($new_instance['topCount']);
		$instance['Country'] =  strip_tags($new_instance['Country']);
		$instance['feedUpdateIntervall'] =  strip_tags($new_instance['feedUpdateIntervall']);
		$instance['feedCache'] = '';
		$instance['feedUpdateTime'] = '';
		$instance['appKind'] =  strip_tags($new_instance['appKind']);
		
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'freePaid' => '', 'topCount' => '', 'Country' => '', 'feedCache' => '', 'feedUpdateTime' => '', 'feedUpdateIntervall' => '', 'appKind' => '' ) );
		$title = attribute_escape($instance['title']);
		$freePaid = attribute_escape($instance['freePaid']) ;
        $topCount = attribute_escape($instance['topCount']) ;
		$Country = attribute_escape($instance['Country']) ;
		$feedCache = attribute_escape($instance['feedCache']) ;
		$feedUpdateTime = attribute_escape($instance['feedUpdateTime']) ;
		$feedUpdateIntervall= attribute_escape($instance['feedUpdateIntervall']) ;
		$appKind= attribute_escape($instance['appKind']) ;
		
		if ($freePaid == "free" ) {
			$checked = "checked" ;
			} else {
			$checked = "" ;
			}
		
		if ($topCount == "" ) {
			$topCount = "10"  ;
			}
			
		if ($title == "" ) {
			$title = "Top Apps"  ;
			}

		if ($Country == "" ) {
			$Country = "AT"  ;
			}
			
		if ($feedUpdateIntervall == "" ) {
			$feedUpdateIntervall = "60"  ;
			}	
			
		if ($appKind == "" ) {
			$appKind = "topfreeapplications";
			}		
							
		$Countries = getCountries();
		
		$appKinds = array(
		"topfreeapplications" => __("iPhone kostenlos","appstore"),
		"toppaidapplications" => __("iPhone kostenpflichtig","appstore"),
		"topgrossingapplications" => __("iPhone umsatzstark","appstore"),	
		"topfreeipadapplications" => __("iPad kostenlos","appstore"),
		"toppaidipadapplications" => __("iPad kostenpflichtig","appstore"),
		"topgrossingipadapplications" => __("iPad umsatzstark","appstore"),		
		"newapplications" => __("neue Apps","appstore"),
		"newfreeapplications" => __("neue kostenlose Apps","appstore"),
		"newpaidapplications" => __("neue kostenpflichtige Apps","appstore"),		
		"topmacapps" => __("Mac alle","appstore"),
		"topfreemacapps" => __("Mac kostenlos","appstore"),
		"topgrossingmacapps" => __("Mac Umsatzstark","appstore"),
		"toppaidmacapps" => __("Mac kostenpflichtig","appstore")
		);
		asort($appKinds);
			
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('topCount'); ?>">Top (Anzahl): <input class="widefat" id="<?php echo $this->get_field_id('topCount'); ?>" name="<?php echo $this->get_field_name('topCount'); ?>" type="text" value="<?php echo attribute_escape($topCount); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('Country'); ?>">Country: <select class="widefat" name="<?php echo $this->get_field_name('Country'); ?>" id="<?php echo $this->get_field_id('Country'); ?>"><br />"; 
										<?php
										foreach($Countries as $Key => $Countryname)
										{
											echo '<option value="'.$Key.'"';
											if(attribute_escape($Country) == $Key) {
												echo ' selected="selected"';
											}
											echo ">".$Countryname."</option>";
										}
										
										?>
										</select></label></p>	
										
		<p><label for="<?php echo $this->get_field_id('appKind'); ?>">Typ: <select class="widefat" name="<?php echo $this->get_field_name('appKind'); ?>" id="<?php echo $this->get_field_id('appKind'); ?>"><br />"; 
								<?php
								foreach($appKinds as $Key => $appKindName)
								{
									echo '<option value="'.$Key.'"';
									if(attribute_escape($appKind) == $Key) {
										echo ' selected="selected"';
									}
									echo ">".$appKindName."</option>";
								}
								
								?>
								</select></label></p>	
															
					
		<p><label for="<?php echo $this->get_field_id('feedUpdateIntervall'); ?>">Updateintervall in Sec <input class="widefat" id="<?php echo $this->get_field_id('feedUpdateIntervall'); ?>" name="<?php echo $this->get_field_name('feedUpdateIntervall'); ?>" type="text" value="<?php echo attribute_escape($feedUpdateIntervall); ?>" /></label></p>
		<?php
		
	}
	
	function widget_control()
	{
		_e("Konfiguration bei den Settings");
	}
}

function wp_AppStoreWidget_init() {
	        if ( !is_blog_installed() )
	                return;
			register_widget('WP_AppStore_Widget');
}
add_action('init', 'wp_AppStoreWidget_init', 1);
