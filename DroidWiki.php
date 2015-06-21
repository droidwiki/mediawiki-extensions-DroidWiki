<?php
/**
 * DroidWiki License
 * Copyright (c) 2014 Florian Schmidt
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This is an extension for Mediawiki and can not run standalone.' );
}

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'DroidWiki',
	'author' => 'Florian Schmidt',
	'url' => 'http://www.droidwiki.de',
	'descriptionmsg' => 'droidwiki-desc',
	'version'  => '1.0.0',
	'license-name' => "MIT",
);

require_once __DIR__ . '/resources/Resources.php';
// Autoload Classes
$wgAutoloadClasses[ 'DroidWikiHooks' ] = __DIR__ . '/DroidWiki.hooks.php';

// Hooks
$wgHooks['SkinTemplateOutputPageBeforeExec'][] =
	'DroidWikiHooks::onSkinTemplateOutputPageBeforeExec';
$wgHooks['BeforePageDisplay'][] = 'DroidWikiHooks::onBeforePageDisplay';
$wgHooks['SkinAfterContent'][] = 'DroidWikiHooks::onSkinAfterContent';
