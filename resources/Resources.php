<?php
// path template
$wgDWResourcePath = array(
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'DroidWiki/resources'
);

$wgResourceModules += array(
	'ext.DroidWiki.adstyle' => $wgDWResourcePath + array(
		'styles' => array( 'ext.DroidWiki.adstyle/droidwikiVectorAdStyle.less' ),
		'position' => 'top',
	),

	'ext.DroidWiki.adstyle.category' => $wgDWResourcePath + array(
		'styles' => array( 'ext.DroidWiki.adstyle.category/droidwikiVectorAdStyleCategory.less' ),
		'position' => 'top',
	),

	'ext.DroidWiki.articleFeedback' => $wgDWResourcePath + array(
		'scripts' => array( 'ext.DroidWiki.articleFeedback/fixArticleFeedback.js' ),
		'position' => 'top',
	),
	'ext.DroidWiki.mainpage.styles' => $wgDWResourcePath + array(
		'styles' => array( 'ext.DroidWiki.mainpage.styles/style.less' ),
		'position' => 'top',
	),
);
