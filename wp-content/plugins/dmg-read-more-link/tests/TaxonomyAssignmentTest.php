<?php
use DMG\ReadMoreLink\Taxonomy;

class TaxonomyAssignmentTest extends WP_UnitTestCase {
    public function test_tags_and_untags_post() {
        // Create post with block
        $post_id = self::factory()->post->create([
            'post_content' => '<!-- wp:dmg/read-more-link /-->'
        ]);

        $post = get_post( $post_id );
        Taxonomy\update_post_taxonomy( $post_id, $post );
        $terms = wp_get_object_terms( $post_id, Taxonomy\TAXONOMY_SLUG, ['fields' => 'slugs'] );
        $this->assertContains( Taxonomy\TAXONOMY_TERM, $terms );

        // Remove block
        wp_update_post([ 'ID' => $post_id, 'post_content' => '' ]);
        Taxonomy\update_post_taxonomy( $post_id, get_post( $post_id ) );
        $terms = wp_get_object_terms( $post_id, Taxonomy\TAXONOMY_SLUG, ['fields' => 'slugs'] );

        $this->assertEmpty( $terms, 'Expected terms to be empty, but returned: '
            . var_export( $terms, true ) );
    }
}