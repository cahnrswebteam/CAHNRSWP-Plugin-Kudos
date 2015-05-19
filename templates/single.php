<?php get_header(); ?>

<main>

<?php get_template_part('parts/headers'); ?>

	<section class="row side-right gutter pad-ends">

		<div class="column one">

			<?php while ( have_posts() ) : the_post(); ?>

			<?php
				$category = wp_get_post_terms( get_the_ID(), 'kudo_categories', array( 'fields' => 'names' ) );
				$mark = wp_get_post_terms( get_the_ID(), 'kudo_marks', array( 'fields' => 'slugs' ) );
			?>

			<img class="cahnrs-kudos-banner" src="<?php echo plugins_url( 'images/banner.gif', dirname(__FILE__) ); ?>" height="184" width="822" />

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<img src="<?php echo plugins_url( 'marks/' . $mark[0] . '.png', dirname(__FILE__) ); ?>" height="60" width="60" class="alignleft" />

				<header class="article-header">
					<hgroup>
						<?php /*if ( spine_get_option( 'articletitle_show' ) == 'true' ) :*/ ?>
						<h1 class="article-title"><?php the_title(); ?></h1>
						<?php /*endif;*/ ?>
						
            <p><?php
							echo get_the_date( 'F j' );
							if ( $category ) {
								echo ', ' . $category[0];
							}
          		//if ( 'yes' !== get_post_meta( get_the_ID(), '_cahnrswp_kudo_sub_anonymous', true ) ) {
							echo ' | ' . 'Submitted by ' . $sub_name = get_post_meta( get_the_ID(), '_cahnrswp_kudo_sub_name', true );
							//}
						?></p>
					</hgroup>
				</header>

				<div class="article-body">
					<?php the_content(); ?>
				</div>

			</article>

			<?php endwhile; ?>

		</div><!--/column-->

		<div class="column two">

			<?php
				if ( is_active_sidebar( 'kudos-sidebar' ) ) {
					dynamic_sidebar( 'kudos-sidebar' );
				}
			?>

		</div><!--/column two-->

	</section>

</main>

<?php get_footer(); ?>