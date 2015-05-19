<?php get_header(); ?>

<main class="spine-archive-index">

<?php get_template_part('parts/headers'); ?>

	<section class="row single gutter">
		<div class="column one">
			<img class="cahnrs-kudos-banner" src="<?php echo plugins_url( 'images/banner.gif', dirname(__FILE__) ); ?>" height="184" width="822" />
      <div class="cahnrs-kudos-archive pagebuilder-item clearfix">
				<div class="kudos">
					<div class="kudo-list">
						<header class="archive-header">
      				<h2><?php echo get_the_date( 'F Y' ); ?> Kudos</h2>
						</header>
						<?php while ( have_posts() ) : the_post(); ?>
						<?php
							$category = wp_get_post_terms( get_the_ID(), 'kudo_categories', array( 'fields' => 'names' ) );
							$mark = wp_get_post_terms( get_the_ID(), 'kudo_marks', array( 'fields' => 'slugs' ) );
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        			<header class="article-header">
								<hgroup>
            			<h3 class="article-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>, <span><?php
            				echo get_the_date( 'F j' );
										if ( $category ) {
											echo ', ' . $category[0];
										}
            				//if ( 'yes' !== get_post_meta( get_the_ID(), '_cahnrswp_kudo_sub_anonymous', true ) ) {
										echo ' | ' . 'Submitted by ' . $sub_name = get_post_meta( get_the_ID(), '_cahnrswp_kudo_sub_name', true );
										//}
									?></span>
    	      		</hgroup>
      	  		</header>
  						<div class="article-body">
    						<?php the_content(); ?>
	  					</div>
						</article>
						<?php endwhile; ?>
					</div>
				</div>
				<?php
					if ( is_active_sidebar( 'kudos-sidebar' ) ) {
						dynamic_sidebar( 'kudos-sidebar' );
					}
				?>
			</div>
		</div>
	</section>

<?php

/* @type WP_Query $wp_query */
global $wp_query;

$big = 99164;
$args = array(
	'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format'       => 'page/%#%',
	'total'        => $wp_query->max_num_pages, // Provide the number of pages this query expects to fill.
	'current'      => max( 1, get_query_var('paged') ), // Provide either 1 or the page number we're on.
);

?>
	<footer class="main-footer archive-footer pad-ends">
		<section class="row side-right pager prevnext gutter">
			<div class="column one">
				<?php echo paginate_links( $args ); ?>
			</div>
			<div class="column two">
				<!-- intentionally empty -->
			</div>
		</section><!--pager-->
	</footer>

	<?php get_template_part( 'parts/footers' ); ?>

</main>
<?php

get_footer();