<?php

namespace DroidWiki;

use Html;
use IContextSource;
use Skin;

class FooterLinks {
	private const LINKS = [ 'developers', 'imprint' ];

	public function provideLinks( Skin $skin, string $key, array &$footerlinks ): void {
		if ( $key !== 'places' ) {
			return;
		}

		foreach ( self::LINKS as $linkName ) {
			$this->provideLink( $linkName, $skin->getContext(), $footerlinks );
		}
	}

	private function provideLink(
		string $linkName, IContextSource $context, &$footerlinks
	): void {
		$destination =
			Skin::makeInternalOrExternalUrl( $context->msg( "droidwiki-$linkName-url" )
				->inContentLanguage()
				->text() );
		$link =
			Html::element( 'a', [ 'href' => $destination ],
				$context->msg( "droidwiki-$linkName" )->text() );
		$footerlinks[$linkName] = $link;
	}
}
