<?php 
add_filter('rewrite_rules_array','wp_insertAppStoreRewriteRules');
add_filter('query_vars','wp_insertAppStoreRewriteQueryVars');
add_filter('init','flushRules');

// Remember to flush_rules() when adding rules
function flushRules(){
	global $wp_rewrite;
   	$wp_rewrite->flush_rules();
}

// Adding a new rule
function wp_insertAppStoreRewriteRules($rules)
{
	$newrules = array();
	//echo  WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY.'/AppStore.php?id='; 
	$newrules['app/(\d*)$'] = WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY.'/AppStore.php?id=$matches[1]';
	return $newrules + $rules;
}

// Adding the id var so that WP recognizes it
function wp_insertAppStoreRewriteQueryVars($vars)
{
    array_push($vars, 'id');
    return $vars;
}
//http://www.touchtalk.at/wp-content/plugins/AppStore/AppStore.php?id=348177604
// http://codex.wordpress.org/Function_Reference/WP_Rewrite
//A Quick and dirty example for rewriting http://mysite/project/1 into http://mysite/index.php?pagename=project&id=1:
?>