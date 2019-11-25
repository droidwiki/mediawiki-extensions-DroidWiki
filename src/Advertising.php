<?php

namespace DroidWiki;

use Html;
use OutputPage;
use QuickTemplate;
use Skin;

class Advertising {
	const ADSENSE_AD_PUSH_CODE = '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
	const ADSENSE_AD_CLIENT = 'ca-pub-4622825295514928';

	private $skin;

	public function __construct( Skin $skin ) {
		$this->skin = $skin;
	}

	public function rightAdBanner( QuickTemplate $template ) {
		if ( $this->skin->getSkinName() === 'vector' && $this->shouldShowRightAdBanner() ) {
			$this->addAdCodeToBodyText( $template );
		}
	}

	private function shouldShowRightAdBanner() {
		global $wgNoAdSites, $wgDroidWikiAdDisallowedNamespaces, $wgDroidWikiNoAdSites;

		if ( is_array( $wgNoAdSites ) ) {
			$wgDroidWikiNoAdSites = array_merge( $wgDroidWikiNoAdSites, $wgNoAdSites );
		}

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

		return !( $this->skin->getUser()->isLoggedIn() );
	}

	private function addAdCodeToBodyText( QuickTemplate $tpl ) {
		$adContent = Html::openElement( 'aside', [
				'id' => 'adContent',
				'class' => 'mw-body-rightcontainer',
			] ) . Html::element( 'script', [
				'async',
				'src' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
			] ) . Html::element( 'ins', [
				'class' => 'adsbygoogle',
				'style' => 'display:inline-block;width:160px;height:600px',
				'data-ad-client' => self::ADSENSE_AD_CLIENT,
				'data-ad-slot' => '8031689899',
			] ) . self::ADSENSE_AD_PUSH_CODE . Html::closeElement( 'aside' );

		$tpl->data['bodytext'] = $adContent . $tpl->data['bodytext'];
		$tpl->getSkin()->getOutput()->addModuleStyles( [ 'ext.DroidWiki.adstyle' ] );
	}

	public function setupBeforePageDisplay( OutputPage $out ) {
		$out->addHTML( Html::element( 'script', [
			'async',
			'data-ad-client' => 'ca-pub-4622825295514928',
			'src' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
		] ) );
		$out->addHTML( '<script>
		(adsbygoogle = window.adsbygoogle || []).push({
			google_ad_client: "' . self::ADSENSE_AD_CLIENT . '",
			enable_page_level_ads: true
		});
		</script>' );
	}

}
