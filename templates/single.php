<?php get_header(); ?>

<main>

<?php get_template_part('parts/headers'); ?>

	<section class="row side-right gutter pad-ends">

		<div class="column one">

			<?php while ( have_posts() ) : the_post(); ?>

			<img class="cahnrs-kudos-banner" src="<?php echo plugins_url( 'images/banner.gif', dirname(__FILE__) ); ?>" height="184" width="822" />

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<header class="article-header">
					<hgroup>
						<?php /*if ( spine_get_option( 'articletitle_show' ) == 'true' ) :*/ ?>
						<h1 class="article-title"><?php the_title(); ?></h1>
						<?php /*endif;*/ ?>
            <p><?php echo get_the_date( 'F j' ); ?>
            <?php
          		if ( 'yes' !== get_post_meta( get_the_ID(), '_cahnrswp_kudo_sub_anonymous', true ) ) {
								echo ' | ' . 'Submitted by ' . $sub_name = get_post_meta( get_the_ID(), '_cahnrswp_kudo_sub_name', true );
							}
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