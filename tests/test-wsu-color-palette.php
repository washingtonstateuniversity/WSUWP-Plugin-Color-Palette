<?php

class WSU_Color_Palette_Test extends WP_UnitTestCase {
	/**
	 * A page created without the assignment of a color palette should have a default palette assigned.
	 */
	public function test_page_default_color_palette() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'page', 'post_title' => 'Test Page' ) );
		$this->go_to( home_url( '?p=' . $post_id ) );
		$this->assertContains( 'wsu-palette-default', get_body_class() );
		$this->assertContains( 'wsu-palette-text-default', get_body_class() );
	}

	/**
	 * Posts cannot be assigned a palette, so they will always have the default applied.
	 */
	public function test_post_default_color_palette() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_title' => 'Test Post' ) );
		$this->go_to( home_url( '?p=' . $post_id ) );
		$this->assertContains( 'wsu-palette-default', get_body_class() );
		$this->assertContains( 'wsu-palette-text-default', get_body_class() );
	}
}