<?php
// Testing Page
// WP_Query for Recipe Post Type
// FIXME: Wrap in a function_exists('activate_wp_recipe_maker_premium') conditional...
if (isset($_GET['submit'])) {
	$search_term = get_query_var('search');
	$course_search = esc_html($_GET['wprm_course']);
} else {
	$search_term = $course_search = '';
} ?>

<form action="" method="GET">
	<label for="search">
		Ingredient Search:
		<input type="text" name="search" value="<?php echo $search_term; ?>">
	</label>
	<?php
	// Get Taxonomies
	$wprm_courses = get_terms([ 'taxonomy' => 'wprm_course' ]);
	echo "<label class='screen-reader-text' for='course'>Meal Type/Course: </label><select class='form-control' name='wprm_course' id='course'><option value=''>-- Any Meal Type/Course --</option>";
	foreach ($wprm_courses as $wprm_course) {
		$wprm_course_slug = strtolower(esc_html($wprm_course->name));
		echo "<option value='" . str_replace(' ', '-', $wprm_course_slug) . "'" . (($course_search == $wprm_course_slug) ? 'selected' : '') . ">" . esc_html($wprm_course->name) ."</option>";
	}
	echo "</select>"; 

	?>
	<input type="submit" name="submit" value="Search">
</form>

<?php
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
			[
				'taxonomy' => 'wprm_course',
				'field'    => 'slug',
				'terms'    => $course_search,
			]
		];
	}

	$the_query = new WP_Query($args);
	// The Loop
	if ($the_query->have_posts()) {
		?>
		<h2>Recipes with "<?php echo $search_term; ?>"</h2>
		<?php if ($course_search != '' ) { ?>
			<p>Meal Type: <?php echo $course_search; ?></p>
		<?php } ?>
		<?php
		echo '<ul class="recipe-list">';
		while ($the_query->have_posts()) {
			$the_query->the_post();
			echo '<li class="cat-item cat-item-'.get_the_ID().'">';
			echo '<a class="recipe-listing" href="'.get_permalink().'">'.get_the_title().'</a>';
			echo '</li>';
		}
		echo '</ul>';
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		echo '<p>We do not currently have any recipes matching the criteria. Please adjust the search criteria above.</p>';
	}
} ?>

<hr />

<?php
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
	echo '<h2>All Recipes</h2>';
	echo '<ul class="recipe-list">';
	while ( $recipes->have_posts() ) {
		$recipes->the_post();

		echo '<li class="cat-item cat-item-'.get_the_ID().'">';
		echo '<a class="recipe-listing" href="'.get_permalink().'">'.get_the_title().'</a>';
		echo '</li>';
	}
	echo '</ul>';
} else {
	echo 'No Recipes Found';
}

// Restore original Post Data
wp_reset_postdata();

?>
