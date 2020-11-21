<?php

namespace DroidWiki;

use Html;
use OutputPage;
use QuickTemplate;
use Skin;

class Advertising {
	const ADSENSE_AD_CLIENT = 'ca-pub-4622825295514928';
	const AD_SKINS = [ 'vector', 'timeless' ];

	private $skin;

	public function __construct( Skin $skin ) {
		$this->skin = $skin;
	}

	private function shouldShowAds(): bool {
		global $wgDroidWikiAdDisallowedNamespaces, $wgDroidWikiNoAdSites;

		$urlTitle = $this->skin->getRequest()->getText( 'title' );
		if ( $wgDroidWikiNoAdSites && in_array( $urlTitle, $wgDroidWikiNoAdSites ) ) {
			return false;
		}

		if ( in_array( $this->skin->getTitle()->getNamespace(),
			$wgDroidWikiAdDisallowedNamespaces ) ) {
			return false;
		}

		if ( !$this->skin->getOutput()->isArticleRelated() ) {
			return false;
		}

		return !$this->skin->getUser()->isLoggedIn();
	}

	public function setupBeforePageDisplay( OutputPage $out ): void {
		if ( !$this->shouldShowAds() ) {
			return;
		}

		$out->addModuleStyles( [ 'ext.DroidWiki.adstyle' ] );
		$out->addHTML( Html::element( 'script', [
			'async',
			'defer',
			'src' => 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
		] ) );
		$out->addHTML( '<script>
		(adsbygoogle = window.adsbygoogle || []).push({
			google_ad_client: "' . self::ADSENSE_AD_CLIENT . '",
			enable_page_level_ads: true
		});
		</script>' );
	}
}
