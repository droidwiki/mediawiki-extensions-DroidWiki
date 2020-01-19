<?php

namespace DroidWiki;

use Html;
use IContextSource;
use QuickTemplate;
use Skin;

class FooterLinks {
	const LINKS = [ 'developers', 'imprint' ];

	public function provideLinks( IContextSource $context, QuickTemplate $template ): void {
		foreach ( self::LINKS as $linkName ) {
			$this->provideLink( $linkName, $context, $template );
		}
	}

	private function provideLink(
		string $linkName, IContextSource $context, QuickTemplate $template
	): void {
		$destination =
			Skin::makeInternalOrExternalUrl( $context->msg( "droidwiki-$linkName-url" )
				->inContentLanguage()
				->text() );
		$link =
			Html::element( 'a', [ 'href' => $destination ],
				$context->msg( "droidwiki-$linkName" )->text() );
		$template->set( $linkName, $link );
		$template->data['footerlinks']['places'][] = $linkName;
	}
}
