<?php

namespace DroidWiki;

use FauxRequest;
use MediaWikiUnitTestCase;
use OutputPage;
use QuickTemplate;
use Skin;
use Title;
use User;

class AdvertisingTest extends MediaWikiUnitTestCase {
	/**
	 * @covers \DroidWiki\Advertising::rightAdBanner
	 */
	public function testRightAdBannerVector() {
		$skin = $this->buildSkinContext();
		$skin->method( 'getSkinName' )->willReturn( 'vector' );
		$tpl =
			$this->getMockBuilder( QuickTemplate::class )->disableOriginalConstructor()->getMock();
		$tpl->data['bodytext'] = '';
		$ads = new Advertising( $skin );

		$ads->rightAdBanner( $tpl );

		$this->assertNotEmpty( $tpl->data['bodytext'] );
	}

	private function buildSkinContext( $articleRelated = true ) {
		$skin = $this->getMockBuilder( Skin::class )->disableOriginalConstructor()->getMock();
		$skin->method( 'getRequest' )->willReturn( new FauxRequest( [ 'title' => 'SOME_TITLE' ] ) );
		$skin->method( 'getTitle' )->willReturn( Title::makeTitle( NS_MAIN, 'SOME_TITLE' ) );
		$outputPage =
			$this->getMockBuilder( OutputPage::class )->disableOriginalConstructor()->getMock();
		$outputPage->method( 'isArticleRelated' )->willReturn( $articleRelated );
		$skin->method( 'getOutput' )->willReturn( $outputPage );
		$user = $this->getMockBuilder( User::class )->getMock();
		$user->method( 'isLoggedIn' )->willReturn( false );
		$skin->method( 'getUser' )->willReturn( $user );

		return $skin;
	}

	/**
	 * @covers \DroidWiki\Advertising::rightAdBanner
	 */
	public function testRightAdBannerTimeless() {
		$skin = $this->buildSkinContext();
		$skin->method( 'getSkinName' )->willReturn( 'timeless' );
		$tpl =
			$this->getMockBuilder( QuickTemplate::class )->disableOriginalConstructor()->getMock();
		$tpl->data['bodytext'] = '';
		$ads = new Advertising( $skin );

		$ads->rightAdBanner( $tpl );

		$this->assertNotEmpty( $tpl->data['bodytext'] );
	}

	/**
	 * @covers \DroidWiki\Advertising::rightAdBanner
	 */
	public function testRightAdBannerNoAdSite() {
		global $wgDroidWikiNoAdSites;
		$wgDroidWikiNoAdSites = [ 'SOME_TITLE' ];
		$skin = $this->buildSkinContext();
		$skin->method( 'getSkinName' )->willReturn( 'vector' );
		$tpl =
			$this->getMockBuilder( QuickTemplate::class )->disableOriginalConstructor()->getMock();
		$tpl->data['bodytext'] = '';
		$ads = new Advertising( $skin );

		$ads->rightAdBanner( $tpl );

		$this->assertEmpty( $tpl->data['bodytext'] );
	}

	/**
	 * @covers \DroidWiki\Advertising::rightAdBanner
	 */
	public function testRightAdBannerNoAdNamespace() {
		global $wgDroidWikiAdDisallowedNamespaces;
		$wgDroidWikiAdDisallowedNamespaces = [ NS_MAIN ];
		$skin = $this->buildSkinContext();
		$skin->method( 'getSkinName' )->willReturn( 'vector' );
		$tpl =
			$this->getMockBuilder( QuickTemplate::class )->disableOriginalConstructor()->getMock();
		$tpl->data['bodytext'] = '';
		$ads = new Advertising( $skin );

		$ads->rightAdBanner( $tpl );

		$this->assertEmpty( $tpl->data['bodytext'] );
	}

	/**
	 * @covers \DroidWiki\Advertising::rightAdBanner
	 */
	public function testRightAdBannerNonArticle() {
		$skin = $this->buildSkinContext( false );
		$skin->method( 'getSkinName' )->willReturn( 'vector' );
		$tpl =
			$this->getMockBuilder( QuickTemplate::class )->disableOriginalConstructor()->getMock();
		$tpl->data['bodytext'] = '';
		$ads = new Advertising( $skin );

		$ads->rightAdBanner( $tpl );

		$this->assertEmpty( $tpl->data['bodytext'] );
	}

	protected function setUp(): void {
		global $wgDroidWikiAdDisallowedNamespaces, $wgNoAdSites, $wgDroidWikiNoAdSites;

		parent::setUp();

		$wgDroidWikiAdDisallowedNamespaces = [];
		$wgDroidWikiNoAdSites = [];
		$wgNoAdSites = [];
	}
}
