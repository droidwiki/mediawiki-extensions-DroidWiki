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
		if ( $sk->getSkinName() === 'vector' ) {
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
