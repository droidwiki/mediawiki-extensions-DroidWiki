<?php
class DroidWikiHooks {
	private static $adAlreadyAdded = false;
	/**
	 * SkinTemplateOutputPageBeforeExec hook handler. Adds ad to Vector skin.
	 *
	 * @param SkinTemplate $sk
	 * @param QuickTemplate $tpl
	 */
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

			$out->mBodytext = $adContent . $out->mBodytext;
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

	/**
	 *
	 */
	public static function onSkinAfterContent( &$data, Skin $sk ) {
		if ( !self::checkShowAd( $sk, 'bottom' ) ) {
			return;
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
			$sk->getOutput()->addHTML(
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
			    Html::closeElement( 'div' )
			);
		} else {
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
	}

	/**
	 * Checks whether it's allowed to show advertising banners on this page.
	 * The check includes the actual login state of the user and the actual site requested.
	 * You can disallow the display of ads on specific pages using the $wgNoAdSites array in
	 * LocalSettings.php
	 * @param boolean $showtologin Is the ad block, the check is requested from, only visible
	 * for logged in users, set this to true, otherwise this will be false and the function gives
	 * only true, if the user is logged out.
	 * @return boolean
	 */
	private static function checkShowAd( SkinTemplate $sk, $position = 'right' ) {
		global $wgNoAdSites, $wgDroidWikiAdDisallowedNamespaces;

		$loggedIn = $sk->getUser()->isLoggedIn();
		// get the URL title (don't use title object, the configuration $wgNoAdSites
		// in LocalSettings specifies url title
		$urltitle = $sk->getRequest()->getText( 'title' );
		if (
			$wgNoAdSites &&
			!in_array( $urltitle, $wgNoAdSites ) &&
			!in_array( $sk->getTitle()->getNamespace(), $wgDroidWikiAdDisallowedNamespaces ) &&
			$sk->getOutput()->isArticleRelated()
		) {
			switch ( $position ) {
				case 'right':
					return !$loggedIn;
					break;
				case 'bottom':
					return $loggedIn;
					break;
			}
		}
		return false;
	}

	/**
	 * BeforePageDisplay hook handler.
	 *
	 * @param OutputPage $out
	 * @param SkinTemplate $sk
	 */
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

		if (
			ExtensionRegistry::getInstance()->isLoaded( 'MobileFrontend' ) &&
			MobileContext::singleton()->shouldDisplayMobileView()
		) {
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

	/**
	 * RequestContextCreateSkin hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/RequestContextCreateSkin
	 *
	 * @param IContextSource $context
	 * @param Skin $skin
	 * @return bool
	 */
	public static function onRequestContextCreateSkin( $context, &$skin ) {
		// FIXME: Temporary variables, will be deprecated in core in the future
		global $wgHTMLFormAllowTableFormat, $wgUseMediaWikiUIEverywhere;

		if ( $context->getTitle()->isMainPage() ) {
			// Turn on MediaWiki UI styles, needed for a better look of Inputbox
			// FIXME: Remove when this becomes the default.
			$wgUseMediaWikiUIEverywhere = true;
		}
	}

	/**
	 * Override the copyright message with a nicer one.
	 *
	 * @param Title $title
	 * @param string $type
	 * @param string $msg
	 * @param $link
	 *
	 * @return bool
	 */
	public static function onSkinCopyrightFooter( $title, $type, &$msg, &$link ) {
		global $wgRightsUrl;

		if( strpos( $wgRightsUrl, 'creativecommons.org/licenses/by-sa/3.0' ) !== false ) {
			if ( $type !== 'history' ) {
				$msg = 'droidwiki-copyright';
			}
		}

		return true;
	}

	/**
	 * PageContentLanguage hook handler. Changes the langugae object to the correct one.
	 *
	 * @param Title $title
	 * @param Language &$pageLang
	 * @param Language $userLang
	 */
	public static function onPageContentLanguage( Title $title, Language &$pageLang, $userLang  ) {
		// FIXME: temporary hack for T121666, this shouldn't be needed
		if ( strpos( $title->getText(), 'Android Training/' ) !== false ) {
			$pageLang = wfGetLangObj( 'en' );
		}
	}
}
