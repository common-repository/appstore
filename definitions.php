<?php 
// Appstore Lookup URL 
define("APPSTORESEARCHLINK", "http://ax.phobos.apple.com.edgesuite.net/WebObjects/MZStoreServices.woa/wa/wsLookup?country=".get_option("AppStore_country")."&lang=".get_option("AppStore_language")."&id=");
// Appstore Search URL 
define("APPSTORESEARCHLINKNAME", "http://ax.phobos.apple.com.edgesuite.net/WebObjects/MZStoreServices.woa/wa/wsSearch?entity=software&country=".get_option("AppStore_country")."&lang=".get_option("AppStore_language")."&term=");
// Picture Caching
define("APPSTORE_PIC_CACHINGTIME", get_option("AppStore_picCache")); // Picture Caching Time in hours
// Content Caching
define("APPSTORE_CONT_CACHINGTIME",get_option("AppStore_dataCache")); // Content Caching Time in Hours
// Bezeichnung Link
define("PLUGIN_BASE_DIRECTORY", basename(dirname(__FILE__)));
define("APPSTORE_TABLENAME", "AppStoreStat");

$AppStore_dlLinkname = get_option("AppStore_dlLinkname");
if ($AppStore_dlLinkname == "") {
	//$AppStore_dlLinkname = "Download (Aff.Link)";
}

define("APPSTORE_DL_LINKNAME",$AppStore_dlLinkname); 

// Plugin definitions
define("APPSTORELINKS_TARGET", AFF_LINK."###URL###");

//Directories
define("APPSTORE_CONTENT_URL", WP_PLUGIN_URL."/".PLUGIN_BASE_DIRECTORY."/cache/"); // URL to Cache Folder (www.site.com/...)
define("APPSTORE_CONTENT_DIR", WP_PLUGIN_DIR."/".PLUGIN_BASE_DIRECTORY."/cache/"); // lokal Path to cache Folder (var/www/htdocs/...)

// Image Mask
define("APPSTORE_IMAGE_MASK_PATH", WP_PLUGIN_DIR."/".PLUGIN_BASE_DIRECTORY."/images/ImageMask.png"); 
// http://ax.phobos.apple.com.edgesuite.net/WebObjects/MZStoreServices.woa/wa/wsLookup?country=ie&lang=de_de&id=284417350
?>