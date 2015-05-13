<?php

class WSU_Color_Palette_Test extends WP_UnitTestCase {
	/**
	 * A page created without the assignment of a color palette should have a default palette assigned.
	 */
	public function test_page_default_color_palette() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'page', 'post_title' => 'Test Page' ) );
		$this->go_to( get_the_permalink( $post_id ) );
		$this->assertContains( 'wsu-palette-default', get_body_class() );
		$this->assertContains( 'wsu-palette-text-default', get_body_class() );
	}

	/**
	 * Posts cannot be assigned a palette, so they will always have the default applied.
	 */
	public function test_post_default_color_palette() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_title' => 'Test Post' ) );
		$this->go_to( get_the_permalink( $post_id ) );
		$this->assertContains( 'wsu-palette-default', get_body_class() );
		$this->assertContains( 'wsu-palette-text-default', get_body_class() );
	}

	/**
	 * A page assigned a palette should return those classes in the body.
	 */
	public function test_page_valid_color_palette() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'page', 'post_title' => 'A Valid Page' ) );
		$response = WSU_Color_Palette::assign_color_palette( 'green', $post_id );
		$this->assertTrue( $response );

		$this->go_to( get_the_permalink( $post_id ) );
		$this->assertContains( 'wsu-palette-green', get_body_class() );
		$this->assertContains( 'wsu-palette-text-green', get_body_class() );
	}

	/**
	 * A page cannot be assigned an invalid palette and should only return default palette classes in the body.
	 */
	public function test_page_invalid_color_palette() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'page', 'post_title' => 'A Valid Page' ) );
		$response = WSU_Color_Palette::assign_color_palette( 'invalid', $post_id );
		$this->assertFalse( $response );

		$this->go_to( get_the_permalink( $post_id ) );
		$this->assertContains( 'wsu-palette-default', get_body_class() );
		$this->assertContains( 'wsu-palette-text-default', get_body_class() );
		$this->assertNotContains( 'wsu-palette-invalid', get_body_class() );
		$this->assertNotContains( 'wsu-palette-text-invalid', get_body_class() );
	}

	public function test_page_valid_filtered_color_palette() {
		add_filter( 'wsu_color_palette_values', array( $this, 'filter_wsu_color_palette' ) );

		$post_id = $this->factory->post->create( array( 'post_type' => 'page', 'post_title' => 'A Filtered Page' ) );
		$response = WSU_Color_Palette::assign_color_palette( 'testing', $post_id );

		$this->assertTrue( $response );

		$this->go_to( get_the_permalink( $post_id ) );
		$this->assertContains( 'wsu-palette-testing', get_body_class() );
		$this->assertContains( 'wsu-palette-text-testing', get_body_class() );
		$this->assertNotContains( 'wsu-palette-default', get_body_class() );
		$this->assertNotContains( 'wsu-palette-text-default', get_body_class() );

		remove_filter( 'wsu_color_palette_values', array( $this, 'filter_wsu_color_palette' ) );
	}

	public function test_page_invalid_filtered_color_palette() {
		add_filter( 'wsu_color_palette_values', array( $this, 'filter_wsu_color_palette' ) );
		$post_id = $this->factory->post->create( array( 'post_type' => 'page', 'post_title' => 'A Filtered Page' ) );
		$response = WSU_Color_Palette::assign_color_palette( 'invalid', $post_id );

		$this->assertFalse( $response );

		$this->go_to( get_the_permalink( $post_id ) );
		$this->assertContains( 'wsu-palette-default', get_body_class() );
		$this->assertContains( 'wsu-palette-text-default', get_body_class() );
		$this->assertNotContains( 'wsu-palette-testing', get_body_class() );
		$this->assertNotContains( 'wsu-palette-text-testing', get_body_class() );

		remove_filter( 'wsu_color_palette_values', array( $this, 'filter_wsu_color_palette' ) );
	}

	public function filter_wsu_color_palette() {
		return array( 'testing' => array( 'name' => 'Testing', 'hex' => '#000000' ) );
	}
}