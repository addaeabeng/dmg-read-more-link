<?php
use DMG\ReadMoreLink\Taxonomy;

class TaxonomyRegistrationTest extends WP_UnitTestCase {
    public function test_hidden_taxonomy_registered() {
        // Register taxonomy
        Taxonomy\register_util_taxonomy();

        $tax = get_taxonomy( Taxonomy\TAXONOMY_SLUG );
        $this->assertFalse( $tax->public );
        $this->assertFalse( $tax->show_ui );
        $this->assertFalse( $tax->hierarchical );
    }
}