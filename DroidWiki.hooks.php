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
			self::checkShowAd( $sk )
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
		if ( !$sk->getOutput()->isArticleRelated() ) {
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
	private static function checkShowAd( SkinTemplate $sk ) {
		global $wgNoAdSites;
		$loginshow = false;
		$loggedIn = $sk->getUser()->isLoggedIn();
		// get the URL title (don't use title object, the configuration $wgNoAdSites
		// in LocalSettings specifies url title
		$urltitle = $sk->getRequest()->getText( 'title' );
		if (
			!$loggedIn &&
			$wgNoAdSites &&
			!in_array( $urltitle, $wgNoAdSites )
		) {
			return true;
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
	}
}
