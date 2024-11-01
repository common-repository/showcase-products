<?php get_header(); ?>
<div id="primary" class="content-area">
<div id="content" class="site-content" role="main">
		
		<div class="product-details"><!--product-details-->
		<div class="col-sm-5">
<?php  while ( have_posts() ) : the_post(); ?>
<div class="view-product">
<?php the_post_thumbnail( 'large' );  ?>
</div> </div> 
<div class="col-sm-7">
<h2><?php the_title(); ?></h2>
	<span>								
<?php $price = get_post_meta( get_the_ID(), '_price', true );
// check if the custom field has a value
if( ! empty( $price ) ) {
  echo '<h2 style="color:orange">' .$price . '</h2>';
}?>
</span>
<?php the_content();?>
					<div class="category-tab shop-details-tab"><!--category-tab-->
						

					</div>
	
	
	
<?php
				
endwhile;
 ?>
</div>
</div><!--/product-details-->
 		</div><!-- #content -->
	</div><!-- #primary -->
 <?php get_footer(); ?>
