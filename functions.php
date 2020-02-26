add_shortcode('ajaxPosts', 'ajaxPosts');
function ajaxPosts() {
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$posts_per_page = 1;
	$args = array(
		'post_type'			=>	'banner_slides',
		'posts_per_page'	=>	$posts_per_page,
		'post_status'		=>	'publish',
		'orderby'			=> 'post_date',
		'order'         	=> 'DESC',
		'paged'				=> $paged
	);
	$query = new WP_Query($args);
	$tp = $query->max_num_pages;
	if($query->have_posts()):
		?>
		<div class='add-data'>
			<?php
			while($query->have_posts()): $query->the_post();
				echo "<p class='mt-5 '>" . get_the_title() . "</p>";
			endwhile;
			?>
			<button class='load-more'>Load More Posts</button>
		</div>
		<?php
	endif;
}
add_action('wp_footer', 'scripts');
function scripts() {
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript">
		jQuery(document).ready( function($) {
			var ajaxUrl = "<?php echo admin_url('admin-ajax.php')?>";
			var ppp = 1;
			var page = 2;
			jQuery(document).on("click",".load-more", function() {
				var obj = jQuery(this);
				jQuery(".load_more").attr("disabled",true);
				jQuery.post(ajaxUrl, {
					action: "load_posts",
					page: page,
					ppp: ppp
				})
				.success(function(posts) {
					page++;
					jQuery(".add-data").append(posts);
					obj.remove();
				});
			});
		});
	</script>
	<?php
}

function load_posts() {
	$ppp = (isset($_POST["ppp"])) ? $_POST["ppp"] : 1;
	$page = (isset($_POST['page'])) ? $_POST['page'] : 1;

	$args = array(
		'post_type' 		=> 'banner_slides',
		'post_status'		=>	'publish',
		'orderby'			=> 'post_date',
		'order'         	=> 'DESC',
		'posts_per_page' 	=> $ppp,
		'paged'    			=> $page
	);
	$loop = new WP_Query($args);
	$out = '';
	if($loop->have_posts()):
		while($loop->have_posts()): $loop->the_post();
			$out .= '<p>'.get_the_title().'</p>';
		endwhile;
		if($page < $loop->max_num_pages){
			$out .= '<button class="load-more">Load More Posts</button>';
		}	

	endif;
	wp_reset_postdata();
	die($out);
}
add_action('wp_ajax_nopriv_load_posts','load_posts');
add_action('wp_ajax_load_posts','load_posts');
