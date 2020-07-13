<?php
/**
 * Badge Factor 2
 * Copyright (C) 2019 ctrlweb
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package Badge_Factor_2
 */

/*
 * You can override this template by copying it in your theme, in a
 * badgefactor2/ subdirectory, and modifying it there.
 */

get_header();

$badge  = BadgeFactor2\Models\BadgeClass::get( $badge );
$issuer = BadgeFactor2\Models\Issuer::get( $badge->issuer );
?>
<section id="primary" class="content-area">
	<main id="main" class="site-main">
	<article id="badge-" <?php post_class(); ?>>
		<div class="entry-content">
		<div class="content">
			<h1 class="badge__name"><?php echo $badge->name; ?></h1>
			<div class="badge__container">
				<div class="badge__badge">
					<figure>
						<img class="badge__image" src="<?php echo $badge->image; ?>" alt="<?php echo $badge->name; ?>">
					</figure>
					<div class="badge__issued">
						<h3><?php echo __( 'Issued by', 'badgefactor2' ); ?></h3>
						<a target="_blank" href="<?php echo $issuer->url; ?>"><?php echo $issuer->name; ?></a>
					</div><!-- .badge__issued -->
				</div><!-- .badge__badge -->
				<div class="badge__description">
					<?php echo $badge->description; ?>
				</div><!-- .badge__description -->
			</div><!-- .badge__container -->
		</div><!-- .content -->
	</article>
	</main><!-- #main -->
</section><!-- #primary -->
<?php
get_footer();