<?php

namespace DroidWiki;

use GitInfo;
use Language;
use OutputPage;
use Skin;
use Title;

class Hooks {
	private static $advertising;

	public static function onSkinAddFooterLinks(
		Skin $skin, string $key, array &$footerlinks
	) {
		$links = new FooterLinks();
		$links->provideLinks( $skin, $key, $footerlinks );

		return true;
	}

	private static function advertising( Skin $sk ): Advertising {
		if ( self::$advertising === null ) {
			self::$advertising = new Advertising( $sk );
		}

		return self::$advertising;
	}

	public static function onBeforePageDisplay( OutputPage $out, Skin $sk ) {
		self::advertising( $sk )->setupBeforePageDisplay( $out );
	}

	public static function onGetSoftwareInfo( &$software ) {
		global $IP;

		$gitInfo = new GitInfo( "$IP/../mw-config/mw-config" );
		if ( $gitInfo ) {
			$software['[http://github.com/droidwiki/operations-mediawiki-config.git MWC]'] =
				'[' . $gitInfo->getHeadViewUrl() . ' ' . substr( $gitInfo->getHeadSHA1(), 0, 7 ) .
				']';
		}
	}

	public static function onSkinCopyrightFooter( $title, $type, &$msg, &$link ) {
		global $wgRightsUrl;

		if ( strpos( $wgRightsUrl, 'creativecommons.org/licenses/by-sa/3.0' ) !== false ) {
			if ( $type !== 'history' ) {
				$msg = 'droidwiki-copyright';
			}
		}

		return true;
	}

	public static function onPageContentLanguage( Title $title, Language &$pageLang, $userLang ) {
		// FIXME: temporary hack for T121666, this shouldn't be needed
		if ( strpos( $title->getText(), 'Android Training/' ) !== false ) {
			$pageLang = wfGetLangObj( 'en' );
		}
	}

	/**
	 * SkinTemplateGetLanguageLink hook handler, which adds the interwiki-www css class to the interwiki-de interlanguage link,
	 * which should indicate to the ContentTranslation extension, that the main droidwiki language (german, with interwiki link
	 * de but mapped to www for ContentTranslation) is already translated.
	 */
	public static function onSkinTemplateGetLanguageLink(
		&$languageLink, $languageLinkTitle, Title $title, OutputPage $out
	) {
		if ( strpos( $languageLink['class'], 'interwiki-de' ) === -1 ) {
			return;
		}

		$languageLink['class'] .= ' interwiki-www';
	}
}
