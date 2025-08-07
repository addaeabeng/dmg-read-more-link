<?php
namespace DMG\ReadMoreLink\CLI;

use WP_CLI;
use WP_CLI\Utils;
use WP_Query;
use const DMG\ReadMoreLink\READ_MORE_LINK_BLOCK_NAME;
use const DMG\ReadMoreLink\Taxonomy\TAXONOMY_SLUG;
use const DMG\ReadMoreLink\Taxonomy\TAXONOMY_TERM;

class Read_More_Search {
    /**
     * Return post IDs matching the read-more-link block within a date range.
     *
     * @param string $date_after  YYYY-MM-DD lower bound.
     * @param string $date_before YYYY-MM-DD upper bound.
     * @return int[]              Array of matching post IDs.
     */
    public function get_matching_post_ids( string $date_after, string $date_before ): array {
        $date_query = [
            [
                'after'     => $date_after,
                'before'    => $date_before,
                'inclusive' => true,
            ],
        ];

        $batch_size  = 100;
        $paged       = 1;
        $post_ids    = [];

        do {
            $query_args = [
                'post_type'              => 'any',
                'post_status'            => 'publish',
                'fields'                 => 'ids',
                'posts_per_page'         => $batch_size,
                'paged'                  => $paged,
                'no_found_rows'          => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'date_query'             => $date_query,
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
                'tax_query'              => [
                    [
                        'taxonomy' => TAXONOMY_SLUG,
                        'field'    => 'slug',
                        'terms'    => TAXONOMY_TERM,
                    ],
                ],
            ];

            $query = new WP_Query( $query_args );

            if ( empty( $query->posts ) ) {
                break;
            }

            $post_ids = array_merge( $post_ids, $query->posts );
            $paged++;
        } while ( count( $query->posts ) === $batch_size );

        return $post_ids;
    }

    /**
     * CLI 'search' command entry point.
     * Logs matching post IDs to STDOUT.
     *
     * @param array $args       Positional args (unused).
     * @param array $assoc_args Associative args with --date-before and --date-after.
     */
    public function search( array $args, array $assoc_args ): void {
        $date_before = Utils\get_flag_value( $assoc_args, 'date-before', gmdate( 'Y-m-d' ) );
        $date_after  = Utils\get_flag_value( $assoc_args, 'date-after', gmdate( 'Y-m-d', strtotime( '-30 days' ) ) );

        if ( ! $this->is_valid_date( $date_before ) ) {
            WP_CLI::log( "Invalid date-before '$date_before', defaulting to today." );
            $date_before = gmdate( 'Y-m-d' );
        }
        if ( ! $this->is_valid_date( $date_after ) ) {
            WP_CLI::log( "Invalid date-after '$date_after', defaulting to 30 days ago." );
            $date_after = gmdate( 'Y-m-d', strtotime( '-30 days' ) );
        }

        $ids = $this->get_matching_post_ids( $date_after, $date_before );

        if ( empty( $ids ) ) {
            WP_CLI::log( sprintf( 'No posts found containing the %s block.', READ_MORE_LINK_BLOCK_NAME ) );
        } else {
            foreach ( $ids as $id ) {
                WP_CLI::log( (string) $id );
            }
            WP_CLI::success(
                sprintf( '%d posts found containing the %s block.', count( $ids ), READ_MORE_LINK_BLOCK_NAME )
            );
        }
    }

    /**
     * Return post IDs to tag based on block presence.
     *
     * @param string $post_type Post type filter.
     * @return int[]            Array of post IDs to tag.
     */
    public function get_posts_to_tag( string $post_type ): array {
        $batch_size = 100;
        $paged      = 1;
        $post_ids   = [];

        do {
            $query = new WP_Query([
                'post_type'              => $post_type,
                'post_status'            => 'publish',
                'fields'                 => 'ids',
                'posts_per_page'         => $batch_size,
                'paged'                  => $paged,
                'no_found_rows'          => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
            ]);

            if ( empty( $query->posts ) ) {
                break;
            }

            foreach ( $query->posts as $post_id ) {
                $post = get_post( $post_id );
                if ( has_block( READ_MORE_LINK_BLOCK_NAME, $post->post_content ) ) {
                    $post_ids[] = $post_id;
                }
            }

            $paged++;
        } while ( count( $query->posts ) === $batch_size );

        return $post_ids;
    }

    /**
     * CLI 'reindex' command entry point.
     * Tags posts (or simulates tagging) based on block presence.
     *
     * @param array $args       Positional args (unused).
     * @param array $assoc_args Associative args with --post-type and --dry-run.
     */
    public function reindex( array $args, array $assoc_args ): void {
        $post_type  = Utils\get_flag_value( $assoc_args, 'post-type', 'any' );
        $dry_run    = Utils\get_flag_value( $assoc_args, 'dry-run', false );
        $post_ids   = $this->get_posts_to_tag( $post_type );

        if ( empty( $post_ids ) ) {
            WP_CLI::log( sprintf( 'No posts to tag containing the %s block.', READ_MORE_LINK_BLOCK_NAME ) );
            return;
        }

        foreach ( $post_ids as $post_id ) {
            if ( $dry_run ) {
                WP_CLI::log( sprintf( "[Dry Run] Would tag post ID %d for block %s", $post_id, READ_MORE_LINK_BLOCK_NAME ) );
            } else {
                wp_set_object_terms( $post_id, TAXONOMY_TERM, TAXONOMY_SLUG, false );
            }
        }

        $message = $dry_run
            ? sprintf( '[Dry Run] Would have tagged %d posts containing the %s block.', count( $post_ids ), READ_MORE_LINK_BLOCK_NAME )
            : sprintf( 'Reindexed %d posts tagged with the %s block.', count( $post_ids ), READ_MORE_LINK_BLOCK_NAME );

        WP_CLI::success( $message );
    }

    /**
     * Validates a YYYY-MM-DD date.
     *
     * @param string $date Date string.
     * @return bool True if valid, false otherwise.
     */
    private function is_valid_date( string $date ): bool {
        $d = \DateTime::createFromFormat( 'Y-m-d', $date );
        return $d && $d->format( 'Y-m-d' ) === $date;
    }
}