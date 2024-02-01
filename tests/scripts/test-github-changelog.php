<?php

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../lib/github-changelog.php';

define( 'PR_CHANGELOG_START_MARKER', '<h2>Changelog Description' );
define( 'PR_CHANGELOG_END_MARKER', '<h2>' );
define( 'LINK_TO_PR', false );

class GitHub_Changelog_Test extends TestCase {
	public function test_get_changelog_html(): void {
		$pr = array();

		$pr['body'] = '# Description
		
Changes were made

## A different section

Content here

## Changelog Description

### This is my changelog title

<!-- An HTML Comment -->

Things we did:

* Fixed a bug
* Fixed another bug

## And another section that isn\'t a changelog

Foo Bar!';

		$changelog = get_changelog_html( $pr );

		$this->assertEquals(
			'<h3>This is my changelog title</h3>
<p>Things we did:</p>
<ul>
<li>Fixed a bug</li>
<li>Fixed another bug</li>
</ul>',
			$changelog 
		);
	}

	public function test_get_changelog_categories() {
		define( 'WP_CHANGELOG_CATEGORIES', 'foo,bar,,baz' );

		$categories = get_changelog_categories();

		$expected = array(
			'foo',
			'bar',
			'baz',
		);

		$this->assertEquals( $expected, $categories );
	}

	public function test_build_changelog_request_body() {
		define( 'WP_CHANGELOG_STATUS', 'publish' );

		$title      = 'foo title';
		$content    = 'foo content';
		$tags       = array( 1, 2, 3 );
		$channels   = array( 'foo channel 1' );
		$categories = array( 'foo category', 'bar category' );

		$body = build_changelog_request_body( $title, $content, $tags, $channels, $categories );

		$expected = array(
			'title'           => $title,
			'content'         => $content,
			'excerpt'         => $title,
			'status'          => 'publish',
			'tags'            => implode( ',', $tags ),
			'categories'      => $categories,
			'release-channel' => implode( ',', $channels ),
		);

		$this->assertEquals( $expected, $body );
	}
}
