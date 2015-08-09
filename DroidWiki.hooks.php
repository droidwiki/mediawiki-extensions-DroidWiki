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
		return true;
	}

	/**
	 *
	 */
	public static function onSkinAfterContent( &$data, Skin $sk ) {
		if ( !self::checkShowAd( $sk, 'bottom' ) ) {
			return;
		}

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
		global $wgNoAdSites;
		$loginshow = false;
		$loggedIn = $sk->getUser()->isLoggedIn();
		// get the URL title (don't use title object, the configuration $wgNoAdSites
		// in LocalSettings specifies url title
		$urltitle = $sk->getRequest()->getText( 'title' );
		if (
			$wgNoAdSites &&
			!in_array( $urltitle, $wgNoAdSites ) &&
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
			'ext.DroidWiki.articleFeedback',
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

	}

	public static function onGetSoftwareInfo( &$software ) {
		global $IP;

		$gitHash = SpecialVersion::getGitHeadSha1( "$IP/specialsources/mw-config" );
		if ( $gitHash ) {
			$software['[http://git.go2tech.de/summary/?r=droidwiki/operations/mediawiki-config.git MWC]'] =
				'[http://git.go2tech.de/commit/?r=droidwiki/operations/mediawiki-config.git&h=' .
				$gitHash .
				' ' .
				substr( $gitHash, 0, 7 ) . ']';
		}
	}
}
