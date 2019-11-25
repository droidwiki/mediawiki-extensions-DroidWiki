<?php

namespace DroidWiki;

use Config;
use IContextSource;
use MediaWikiUnitTestCase;
use QuickTemplate;
use RawMessage;

class FooterLinksTest extends MediaWikiUnitTestCase {
	private $context;
	private $template;

	public function setUp(): void {
		parent::setUp();

		$this->context = $this->createMock( IContextSource::class );
		$this->context->method( 'msg' )->willReturnMap( [
			[ 'droidwiki-developers-url', new UnitTestMessage( 'http://developers-url' ) ],
			[ 'droidwiki-developers', new UnitTestMessage( 'Developers' ) ],
			[ 'droidwiki-imprint-url', new UnitTestMessage( 'http://imprint-url' ) ],
			[ 'droidwiki-imprint', new UnitTestMessage( 'Imprint' ) ],
		] );

		$this->template = new TestTemplate();
		$this->template->data = [ 'footerlinks' => [ 'places' => [] ] ];
	}

	/**
	 * @covers       \DroidWiki\FooterLinks::provideLinks
	 * @dataProvider provideLinks
	 * @param $linkName
	 * @param $expectedLink
	 */
	public function testProvidesDevelopersLink( string $linkName, string $expectedLink ) {
		$footerLinks = new FooterLinks();

		$footerLinks->provideLinks( $this->context, $this->template );

		$this->assertContains( $linkName, $this->template->data['footerlinks']['places'] );
		$this->assertEquals( $expectedLink, $this->template->get( $linkName ) );
	}

	public function provideLinks() {
		return [
			[ 'developers', '<a href="http://developers-url">Developers</a>' ],
			[ 'imprint', '<a href="http://imprint-url">Imprint</a>' ],
		];
	}
}

class UnitTestMessage extends RawMessage {
	public function inContentLanguage() {
		return $this;
	}

	public function text() {
		return $this->fetchMessage();
	}
}

class TestTemplate extends QuickTemplate {
	public function __construct( Config $config = null ) {
	}

	public function execute() {
		return '';
	}
}
