<?php
class DroidWikiHooks {
	/**
	 * SkinTemplateOutputPageBeforeExec hook handler. Adds ad to Vector skin.
	 *
	 * @param SkinTemplate $sk
	 * @param QuickTemplate $tpl
	 */
	public static function onSkinTemplateOutputPageBeforeExec(
		SkinTemplate &$sk, QuickTemplate &$tpl
	) {
		if ( $sk->getSkinName() === 'vector' && self::checkShowAd( false, $sk, $tpl ) ) {
			$out = $sk->getOutput();
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
	 * Checks whether it's allowed to show advertising banners on this page.
	 * The check includes the actual login state of the user and the actual site requested.
	 * You can disallow the display of ads on specific pages using the $wgNoAdSites array in
	 * LocalSettings.php
	 * @param boolean $showtologin Is the ad block, the check is requested from, only visible
	 * for logged in users, set this to true, otherwise this will be false and the function gives
	 * only true, if the user is logged out.
	 * @return boolean
	 */
	private static function checkShowAd( $showtologin = false, SkinTemplate $sk, BaseTemplate $tpl ) {
		global $wgNoAdSites;
		$loginshow = false;
		if ( $showtologin && $tpl->data['loggedin'] ) {
			$loginshow = true;
			if ( !empty( $tpl->data['showadlogin'] ) ) {
				return $tpl->data['showadlogin'];
			}
		}
		if ( !$showtologin && !$tpl->data['loggedin'] ) {
			$loginshow = true;
			if ( !empty( $tpl->data['showad'] ) ) {
				return $tpl->data['showad'];
			}
		}
		// get the URL title (don't use title object, the configuration $wgNoAdSites
		// in LocalSettings specifies url title
		$urltitle = $sk->getRequest()->getText( 'title' );
		if ( !in_array( $urltitle, $wgNoAdSites ) && $loginshow ) {
			if ( $showtologin && $tpl->data['loggedin'] ) {
				$tpl->data['showadlogin'] = true;
			}
			if ( !$showtologin && !$tpl->data['loggedin'] ) {
				$tpl->data['showad'] = true;
			}
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
		if ( $sk->getSkinName() === 'vector' ) {
			$out->addModules( 'ext.DroidWiki.adstyle' );
		}
	}
}
