<?php
/*
 * Plugin Name: TCBA Recipe Sorter
 * Plugin URI: https://thecuttingboardacademy.org
 * Description: Sets up a shortcode for the recipe sorting/filtering functionality. 
 * Version: 1.0
 * Author: Cle GiveCamp
 * Author URI: http://clevelandgivecamp.org
 * License: GPL2
 * Text Domain: cle_givecamp_testing
 */

add_shortcode('recipe-sorter', 'tcba_recipe_sorter');
function tcba_recipe_sorter() {
	// FIXME: Wrap in a function_exists('activate_wp_recipe_maker_premium') conditional...

	if (isset($_GET['submit'])) {
		$search_term = get_query_var('search');
		$course_search = esc_html($_GET['wprm_course']);
		$dietary_search = esc_html($_GET['wprm_diet_and_health']);
	} else {
		$search_term = $course_search = '';
	}

	$return = '<form action="" method="GET">
		<label for="search">
			Ingredient Search:
			<input type="text" name="search" value="<?php echo $search_term; ?>">
		</label>';

		// Get Taxonomies
		$wprm_courses = get_terms([ 'taxonomy' => 'wprm_course' ]);
		$return .= "<label class='screen-reader-text' for='course'>Meal Type/Course: </label><select class='form-control' name='wprm_course' id='course'><option value=''>-- Any Meal Type/Course --</option>";
		foreach ($wprm_courses as $wprm_course) {
			$wprm_course_slug = str_replace(' ', '-', strtolower(esc_html($wprm_course->name)));
			$return .= "<option value='" . str_replace(' ', '-', $wprm_course_slug) . "'" . (($course_search == $wprm_course_slug) ? 'selected' : '') . ">" . esc_html($wprm_course->name) ."</option>";
		}
		$return .= "</select>"; 

		$wprm_diet_and_health_terms = get_terms([ 'taxonomy' => 'wprm_diet_and_health' ]);
		$return .= "<label class='screen-reader-text' for='diet_and_health'>Dietary/Health: </label><select class='form-control' name='wprm_diet_and_health' id='diet_and_health'><option value=''>-- Any --</option>";
		foreach ($wprm_diet_and_health_terms as $wprm_diet_and_health) {
			$wprm_diet_and_health_slug = str_replace(' ', '-', strtolower(esc_html($wprm_diet_and_health->name)));
			$return .= "<option value='" . str_replace(' ', '-', $wprm_diet_and_health_slug) . "'" . (($dietary_search == $wprm_diet_and_health_slug) ? 'selected' : '') . ">" . esc_html($wprm_diet_and_health->name) ."</option>";
		}
		$return .= '</select>
			<input type="submit" name="submit" value="Search">
		</form>';

	if (isset($_GET['submit'])) {
		$args = [
			'post_type' => 'wprm_recipe',
			'post_status' => 'publish',
			's' => $search_term,
			'posts_per_page' => -1,
		];

		if ( $course_search != '' ) {
			// Append the args with tax info where applicable.
			$args['tax_query'] = [
				'relation'	   => 'AND',
				[
					'taxonomy' => 'wprm_course',
					'field'    => 'slug',
					'terms'    => $course_search,
				]
			];
		}

		if ( $dietary_search != '' ) {
			// Append the args with tax info where applicable.
			if ( is_array($args['tax_query']) ) {
				$new_args['tax_query'] = [
					'relation'	   => 'AND',
					[
						'taxonomy' => 'wprm_diet_and_health',
						'field'    => 'slug',
						'terms'    => $dietary_search,
					]
				];
				array_push($args['tax_query'], $new_args['tax_query']);
			} else {
				$args['tax_query'] = [
					'relation'	   => 'AND',
					[
						'taxonomy' => 'wprm_diet_and_health',
						'field'    => 'slug',
						'terms'    => $dietary_search,
					]
				];
			}
		}

		$the_query = new WP_Query($args);
		// The Loop
		if ($the_query->have_posts()) {
			$return .= '<h2>Recipes with "' . $search_term . '"</h2>';
			if ($course_search != '' ) { 
				$return .= '<p>Meal Type: ' . $course_search . '</p>';
			}
			$return .= '<ul class="recipe-list">';
			while ($the_query->have_posts()) {
				$the_query->the_post();
				$return .= '<li class="cat-item cat-item-'.get_the_ID().'">
					<a class="recipe-listing" href="'.get_permalink().'">'.get_the_title().'</a>
				</li>';
			}
			$return .= '</ul>';
			/* Restore original Post Data */
			wp_reset_postdata();
		} else {
			$return .= '<p>We do not currently have any recipes matching the criteria. Please adjust the search criteria above.</p>';
		}
	} 

	$return .= '<hr />';

	$args = array (
		'post_type'             => array( 'wprm_recipe' ),
		// 'post_status'        => array( 'publish' ),
		'nopaging'              => true,
		'order'                 => 'ASC',
		'orderby'               => 'menu_order',
		'posts_per_page'		=> 5
	);

	$recipes = new WP_Query( $args );

	if ( $recipes->have_posts() ) {
		$return .= '<h2>All Recipes</h2>';
		$return .= '<ul class="recipe-list">';
		while ( $recipes->have_posts() ) {
			$recipes->the_post();

			$return .= '<li class="cat-item cat-item-'.get_the_ID().'">';
			$return .= '<a class="recipe-listing" href="'.get_permalink().'">'.get_the_title().'</a>';
			$return .= '</li>';
		}
		$return .= '</ul>';
	} else {
		$return .= 'No Recipes Found';
	}

	// Restore original Post Data
	wp_reset_postdata();

	return $return;
	
} ?>