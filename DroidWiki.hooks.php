<?php
class DroidWikiHooks {
	private static $adAlreadyAdded = false;

	public static function onSkinTemplateOutputPageBeforeExec(
		SkinTemplate &$sk, QuickTemplate &$tpl
	) {
		if (
			!self::$adAlreadyAdded &&
			$sk->getSkinName() === 'vector' &&
			self::checkShowAd( $sk, 'right' )
		) {
			$out = $sk->getOutput();
			self::$adAlreadyAdded = true;
			$adContent = Html::openElement(
					'aside',
					array(
						'id' => 'adContent',
						'class' => 'mw-body-rightcontainer',
					)
				) .
				Html::element(
					'script',
					array(
						'async',
						'src' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
					)
				) .
				Html::openElement(
					'ins',
					array(
						'class' => 'adsbygoogle',
						'style' => 'display:inline-block;width:160px;height:600px',
						'data-ad-client' => 'ca-pub-4622825295514928',
						'data-ad-slot' => '8031689899',
					)
				) .
				Html::closeElement( 'ins' ) .
				Html::openElement(
					'script'
				) .
				'(adsbygoogle = window.adsbygoogle || []).push({});' .
				Html::closeElement( 'script' ) .
				Html::closeElement( 'aside' );

			$tpl->data['bodytext'] = $adContent . $tpl->data['bodytext'];
		}

		$lockedPages = array(
			SpecialPage::getTitleFor( 'MobileDiff' )->getRootText()
		);
		// this is the mobile web ad block
		if (
			ExtensionRegistry::getInstance()->isLoaded( 'MobileFrontend' ) &&
			MobileContext::singleton()->shouldDisplayMobileView() &&
			!in_array( $sk->getTitle()->getRootText(), $lockedPages )
		) {
			$tpl->data['bodytext'] = $tpl->data['bodytext'] .
				Html::openElement( 'div', [ 'id' => 'ad-cat', 'class' => 'adsbygoogleCategory' ] ) .
				Html::element(
					'script',
					array(
						'async',
						'src' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
					)
				) .
				Html::openElement(
					'ins',
					array(
						'class' => 'adsbygoogle',
						'style' => 'display:block',
						'data-ad-client' => 'ca-pub-4622825295514928',
						'data-ad-slot' => '6645983899',
						'data-ad-format' => 'horizontal',
					)
				) .
				Html::closeElement( 'ins' ) .
				Html::openElement(
					'script'
				) .
				'(adsbygoogle = window.adsbygoogle || []).push({});' .
				Html::closeElement( 'script' ) .
				Html::closeElement( 'div' );
		}


		$devDestination = Skin::makeInternalOrExternalUrl( $sk->msg( 'droidwiki-developers-url' )->inContentLanguage()->text() );
		$devLink = Html::element(
			'a',
			array( 'href' => $devDestination ),
			$sk->msg( 'droidwiki-developers' )->text()
		);
		$tpl->set( 'developers', $devLink );
		$tpl->data['footerlinks']['places'][] = 'developers';
		$cookieDestination = Skin::makeInternalOrExternalUrl( $sk->msg( 'droidwiki-imprint-url' )->inContentLanguage()->text() );
		$cookieLink = Html::element(
			'a',
			array( 'href' => $cookieDestination ),
			$sk->msg( 'droidwiki-imprint' )->text()
		);
		$tpl->set( 'imprint', $cookieLink );
		$tpl->data['footerlinks']['places'][] = 'imprint';
		return true;
	}

	public static function onSkinAfterContent( &$data, Skin $sk ) {
		if ( !self::checkShowAd( $sk, 'bottom' ) ) {
			return;
		}

		// the desktop ad block is slightly different
		$data = Html::openElement(
				'div',
				array(
					'class' => 'adsbygoogleCategory'
				)
			) .
		        Html::element(
			        'script',
			        array(
				        'async',
				        'src' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
			        )
		        ) .
		        Html::openElement(
			        'ins',
			        array(
				        'class' => 'adsbygoogle',
				        'style' => 'display:block',
				        'data-ad-client' => 'ca-pub-4622825295514928',
				        'data-ad-slot' => '6216454699',
				        'data-ad-format' => 'auto',
			        )
		        ) .
		        Html::closeElement( 'ins' ) .
		        Html::openElement(
			        'script'
		        ) .
		        '(adsbygoogle = window.adsbygoogle || []).push({});' .
		        Html::closeElement( 'script' ) .
		        Html::closeElement( 'div' );
	}

	public static function checkShowAd( SkinTemplate $sk, $position = 'right' ) {
		global $wgNoAdSites, $wgDroidWikiAdDisallowedNamespaces, $wgDroidWikiNoAdSites;

		if ( is_array( $wgNoAdSites ) ) {
			$wgDroidWikiNoAdSites = array_merge( $wgDroidWikiNoAdSites, $wgNoAdSites );
		}

		$urlTitle = $sk->getRequest()->getText( 'title' );
		if ( $wgDroidWikiNoAdSites && in_array( $urlTitle, $wgDroidWikiNoAdSites ) ) {
			return false;
		}

		if ( in_array( $sk->getTitle()->getNamespace(), $wgDroidWikiAdDisallowedNamespaces ) ) {
			return false;
		}

		if ( !$sk->getOutput()->isArticleRelated() ) {
			return false;
		}

		$loggedIn = $sk->getUser()->isLoggedIn();
		switch ( $position ) {
			case 'right':
				return !$loggedIn;
				break;
			case 'bottom':
				return $loggedIn;
				break;
			default:
				return false;
		}
	}

	public static function onBeforePageDisplay( OutputPage $out, Skin $sk ) {
		$modules = array(
			'ext.DroidWiki.adstyle.category'
		);
		if ( $sk->getSkinName() === 'vector' && self::checkShowAd( $sk ) ) {
			$modules[] = 'ext.DroidWiki.adstyle';
		}
		$out->addModules( $modules );

		// main page adjustments
		if ( $out->getTitle()->isMainPage() ) {
			$out->addModuleStyles( 'ext.DroidWiki.mainpage.styles' );
		}

		$out->addHeadItem( 'google_ad_sitelevel',
			'<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>' );
		$out->addHeadItem( 'google_ad_sitelevel_config',
		'<script>
		(adsbygoogle = window.adsbygoogle || []).push({
			google_ad_client: "ca-pub-4622825295514928",
			enable_page_level_ads: true
		});
		</script>' );
	}

	public static function onGetSoftwareInfo( &$software ) {
		global $IP;

		$gitInfo = new GitInfo( "$IP/../mw-config/mw-config" );
		if ( $gitInfo ) {
			$software['[http://git.go2tech.de/?p=droidwiki%2Foperations%2Fmediawiki-config.git MWC]'] =
				'[' .
				$gitInfo->getHeadViewUrl() .
				' ' .
				substr( $gitInfo->getHeadSHA1(), 0, 7 ) . ']';
		}
	}

	public static function onSkinCopyrightFooter( $title, $type, &$msg, &$link ) {
		global $wgRightsUrl;

		if( strpos( $wgRightsUrl, 'creativecommons.org/licenses/by-sa/3.0' ) !== false ) {
			if ( $type !== 'history' ) {
				$msg = 'droidwiki-copyright';
			}
		}

		return true;
	}

	public static function onPageContentLanguage( Title $title, Language &$pageLang, $userLang  ) {
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
	public static function onSkinTemplateGetLanguageLink( &$languageLink, $languageLinkTitle, Title $title, OutputPage $out ) {
		if ( strpos( $languageLink['class'],  'interwiki-de' ) === -1 ) {
			return;
		}

		$languageLink['class'] .= ' interwiki-www';
	}
}
