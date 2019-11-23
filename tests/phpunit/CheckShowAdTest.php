<?php

class CheckShowAdTest extends MediaWikiTestCase {
	const A_TITLE = 'A_TITLE';

	private $loggedOutUser;
	private $aTitle;
	private $anOutputPage;
	private $aTitleWebRequest;
	private $skinTemplate;

	public function setUp() {
		parent::setUp();

		$this->loggedOutUser = new User();

		$this->aTitleWebRequest = $this->getMock( WebRequest::class );
		$this->aTitleWebRequest
			->method( 'getText' )
			->with( 'title' )
			->willReturn( self::A_TITLE );

		$this->aTitle = $this->getMock( Title::class );
		$this->aTitle->method( 'getNamespace' )->willReturn( 0 );

		$this->anOutputPage = $this
			->getMockBuilder( OutputPage::class )
			->disableOriginalConstructor()
			->getMock();
		$this->anOutputPage->method( 'isArticleRelated' )
			->willReturn( true );

		$this->skinTemplate = $this->getMock( SkinTemplate::class );
		$this->skinTemplate->method( 'getRequest' )
			->willReturn( $this->aTitleWebRequest );
		$this->skinTemplate->method( 'getTitle' )
			->willReturn( $this->aTitle );
		$this->skinTemplate->method( 'getOutput' )
			->willReturn( $this->anOutputPage );
	}

	public function test_noAdSiteNotMatching_returnsTrue() {
		$this->setMwGlobals( [
			'wgDroidWikiNoAdSites' => [],
		] );
		$this->skinTemplate->method( 'getUser' )->willReturn( $this->loggedOutUser );

		$actual = DroidWikiHooks::checkShowAd( $this->skinTemplate, 'right' );

		$this->assertTrue( $actual );
	}

	public function test_noAdSiteMatching_returnsFalse() {
		$this->setMwGlobals( [
			'wgDroidWikiNoAdSites' => [ self::A_TITLE ],
		] );

		$actual = DroidWikiHooks::checkShowAd( $this->skinTemplate, 'right' );

		$this->assertFalse( $actual );
	}

	public function test_disallowedNamespaceNotMatching_returnsTrue() {
		$this->setMwGlobals( [
			'wgDroidWikiAdDisallowedNamespaces' => [],
		] );
		$this->skinTemplate->method( 'getUser' )->willReturn( $this->loggedOutUser );

		$actual = DroidWikiHooks::checkShowAd( $this->skinTemplate, 'right' );

		$this->assertTrue( $actual );
	}

	public function test_disallowedNamespaceMatching_returnsFalse() {
		$this->setMwGlobals( [
			'wgDroidWikiAdDisallowedNamespaces' => [ 0 ],
		] );

		$actual = DroidWikiHooks::checkShowAd( $this->skinTemplate, 'right' );

		$this->assertFalse( $actual );
	}

	public function test_isArticleRelated_returnsTrue() {
		$this->skinTemplate->method( 'getUser' )->willReturn( $this->loggedOutUser );

		$actual = DroidWikiHooks::checkShowAd( $this->skinTemplate, 'right' );

		$this->assertTrue( $actual );
	}

	public function test_isNotArticleRelated_returnsFalse() {
		$output = $this->getMockBuilder( OutputPage::class )
			->disableOriginalConstructor()
			->getMock();
		$output->method( 'isArticleRelated' )->willReturn( false );
		$skinTemplate = $this->getMock( SkinTemplate::class );
		$skinTemplate->method( 'getRequest' )
			->willReturn( $this->aTitleWebRequest );
		$skinTemplate->method( 'getTitle' )
			->willReturn( $this->aTitle );
		$skinTemplate->method( 'getOutput' )
			->willReturn( $output );
		$skinTemplate->method( 'getUser' )->willReturn( $this->loggedOutUser );

		$actual = DroidWikiHooks::checkShowAd( $skinTemplate, 'right' );

		$this->assertFalse( $actual );
	}
}
