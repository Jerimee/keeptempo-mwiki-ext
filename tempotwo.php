<?php
# Alert the user that this is not a valid access point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
        echo <<<EOT
			To install this Extension, put the following line in LocalSettings.php: require_once( "\$IP/extensions/tempotwo/tempotwo.php" );
EOT;
        exit( 1 );
}
 
$wgExtensionCredits[ 'specialpage' ][] = array(
        'path' => __FILE__,
        'name' => 'Tempotwo',
        'author' => 'Richir Outreach',
        'url' => 'http://www.reddit.com',
        'descriptionmsg' => 'is amazing',
        'version' => '0.0.2',
);
 
$wgAutoloadClasses[ 'SpecialTempotwo' ] = __DIR__ . '/SpecialTempotwo.php'; # Location of the SpecialMyExtension class (Tell MediaWiki to load this file)
$wgExtensionMessagesFiles[ 'Tempotwo' ] = __DIR__ . '/tempotwo.i18n.php'; # Location of a messages file (Tell MediaWiki to load this file)
$wgExtensionMessagesFiles[ 'TempotwoAlias' ] = __DIR__ . '/tempotwo.alias.php'; # Location of an aliases file (Tell MediaWiki to load this file)
$wgSpecialPages[ 'Tempotwo' ] = 'SpecialTempotwo'; # Tell MediaWiki about the new special page and its class name
$wgSpecialPageGroups[ 'Tempotwo' ] = 'other';

$wgResourceModules['Tempotwo'] = array(
	'localBasePath'=>__DIR__,
	'styles'=>'/modules/tempostyles.css',
);