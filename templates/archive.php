<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
						if ( is_day() ) :
							printf( __( 'Daily Archives: %s', 'sp' ), get_the_date() );

						elseif ( is_month() ) :
							printf( __( 'Monthly Archives: %s', 'sp' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'twentyfourteen' ) ) );

						elseif ( is_year() ) :
							printf( __( 'Yearly Archives: %s', 'sp' ), get_the_date( _x( 'Y', 'yearly archives date format', 'twentyfourteen' ) ) );

						else :
							_e( 'Archives', 'sp' );

						endif;
					?>
				</h1>
			</header><!-- .page-header -->

			<?php global $wp_query; 
					// Start the Loop.
					while ( have_posts() ) : the_post();

						/*
						 * Include the post format-specific template for the content. If you want to
						 * use this in a child theme, then include a file called called content-___.php
						 * (where ___ is the post format) and that will be used instead.
						 */
						//get_template_part( 'content', get_post_format() );
$post_id = get_the_ID();


 echo "<div class='col-sm-4'><div class='product-image-wrapper'><div class='single-products'>
 <div class='productinfo text-center'>";
$src = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'medium' );
$url = $src[0];

echo "<img src='$url'/>"; ?>
<p><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
<h2><?php the_title(); ?></h2></a></p>
<p>Price: <?php echo get_post_meta($post_id, "_price", true); ?></p>
</div>

<div class="product-overlay">
											<div class="overlay-content">
												<h2><?php echo get_post_meta($post_id, "_price", true); ?></h2>
												<p><?php echo get_post_meta($post_id, "_short_description", true); ?></p>
												<a href="<?php the_permalink() ?>" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>View</a>
											</div>
										</div>

								
								</div></div></div>
<?php
endwhile;

 if (function_exists('custom_pagination_archive')) {
        custom_pagination_archive($wp_query->max_num_pages,"",$paged);
     }

    
//wp_pagenavi(); 
echo "</div>";
					//endwhile;
					// Previous/next page navigation.
					
					// echo paginate_links( $args );

				else :
					// If no content, include the "No posts found" template.
					get_template_part( 'content', 'none' );

				endif;
			?>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php


get_footer();
