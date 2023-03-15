<?php

namespace WPD\Statistics;

use WP_Comment;
use WP_Comment_Query;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_Term_Query;
use WP_User;
use WP_User_Query;

final class Helpers {

	/**
	 * @param array $ids
	 *
	 * @return array
	 */
	public static function get_comments_by_ids( array $ids ): array {
		$query = new WP_Comment_Query(
			[
				'comment__in' => self::query_in_array( $ids ),
				'number'      => self::query_per_page( $ids ),
				'orderby'     => 'comment__in',
				'order'       => 'ASC',
			]
		);

		return $query->get_comments();
	}

	/**
	 * @param array $ids
	 *
	 * @return array
	 */
	public static function get_posts_by_ids( array $ids ): array {
		$query = new WP_Query(
			[
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'orderby'             => 'post__in',
				'order'               => 'ASC',
				'post__in'            => self::query_in_array( $ids ),
				'post_type'           => 'any',
				'posts_per_page'      => self::query_per_page( $ids ),
			]
		);

		return $query->get_posts();
	}

	/**
	 * @param array $ids Term taxonomy ID, not just term ID.
	 *
	 * @return array
	 */
	public static function get_terms_by_ids( array $ids ): array {
		$query = new WP_Term_Query(
			[
				'hide_empty'       => false,
				'number'           => self::query_per_page( $ids ),
				'orderby'          => 'term_taxonomy_id',
				'term_taxonomy_id' => self::query_in_array( $ids ),
			]
		);

		return $query->get_terms();
	}

	/**
	 * @param array $ids
	 *
	 * @return array
	 */
	public static function get_authors_by_ids( array $ids ): array {
		$query = new WP_User_Query(
			[
				'count_total' => false,
				'include'     => self::query_in_array( $ids ),
				'number'      => self::query_per_page( $ids ),
				'orderby'     => 'include',
			]
		);

		return $query->get_results();
	}

	/**
	 * @param int[] $ids
	 *
	 * @return int[]
	 */
	public static function query_in_array( array $ids ): array {
		return ! empty( $ids ) ? $ids : [ 0 ];
	}

	/**
	 * @param array $ids
	 *
	 * @return int
	 */
	public static function query_per_page( array $ids ): int {
		return max( 1, count( $ids ) );
	}

	/**
	 * @param WP_Comment $comment
	 *
	 * @return int
	 */
	public static function comment_id( WP_Comment $comment ): int {
		return $comment->comment_ID;
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return int
	 */
	public static function post_id( WP_Post $post ): int {
		return $post->ID;
	}

	/**
	 * @param WP_Term $term
	 *
	 * @return int
	 */
	public static function term_taxonomy_id( WP_Term $term ): int {
		return $term->term_taxonomy_id;
	}

	/**
	 * @param WP_User $user
	 *
	 * @return int
	 */
	public static function user_id( WP_User $user ): int {
		return $user->ID;
	}
}
