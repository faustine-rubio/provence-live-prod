<?php
/**
 * Pied de page personnalisé Provence Live Prod.
 *
 * @package PLP_Astra_Child
 */

defined( 'ABSPATH' ) || exit;

astra_content_bottom();
?>
	</div><!-- .ast-container -->
	</div><!-- #content -->
<?php astra_content_after(); ?>

	<footer class="site-footer plp-footer" id="colophon" itemscope itemtype="https://schema.org/WPFooter">
		<div class="plp-footer__inner ast-container">
			<div class="plp-footer__brand">
				<img class="plp-footer__logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/logo-plp.png' ); ?>" alt="Provence Live Prod" width="260" height="132">
				<div class="plp-footer__brand-text">
					<h2 class="plp-footer__brand-title">Provence Live Prod</h2>
					<p class="plp-footer__tagline">Production de spectacles, booking et promotion.</p>
				</div>
			</div>

			<div class="plp-footer__column">
				<h2 class="plp-footer__title">Nous contacter</h2>
				<ul class="plp-footer__links">
					<li><a class="plp-footer__link-with-icon" href="mailto:provenceliveprod@gmail.com"><svg class="plp-footer__icon" aria-hidden="true" viewBox="0 0 24 24"><path d="M3 5h18v14H3V5Zm2 2v.5l7 4.7 7-4.7V7l-7 4.7L5 7Z" fill="currentColor"/></svg>provenceliveprod@gmail.com</a></li>
					<li><a class="plp-footer__link-with-icon" href="tel:+33695169780"><svg class="plp-footer__icon" aria-hidden="true" viewBox="0 0 24 24"><path d="M6.6 10.5a15.7 15.7 0 0 0 6.9 6.9l2.3-2.3a1 1 0 0 1 1-.2c1 .4 2.1.6 3.2.6a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.4 21 3 13.6 3 4a1 1 0 0 1 1-1h3.1a1 1 0 0 1 1 1c0 1.1.2 2.2.6 3.2a1 1 0 0 1-.2 1l-2.3 2.3Z" fill="currentColor"/></svg>06.95.16.97.80</a></li>
					<li><span class="plp-footer__link-with-icon"><svg class="plp-footer__icon" aria-hidden="true" viewBox="0 0 24 24"><path d="M12 2.8a6.9 6.9 0 0 0-6.9 6.9c0 4.5 6.2 10.3 6.5 10.6.2.2.5.2.7 0 .3-.3 6.5-6.1 6.5-10.6A6.9 6.9 0 0 0 12 2.8Zm0 9.4a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" fill="currentColor"/></svg>Basée à Oraison (04)</span></li>
				</ul>
			</div>

			<div class="plp-footer__column">
				<h2 class="plp-footer__title">Navigation</h2>
				<ul class="plp-footer__links">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a></li>
					<li><a href="<?php echo esc_url( plp_page_url( 'artistes-et-specatcles' ) ); ?>">Artistes et Spectacles</a></li>
					<li><a href="<?php echo esc_url( plp_page_url( 'contact' ) ); ?>">Nous contacter</a></li>
				</ul>
			</div>

			<div class="plp-footer__column plp-footer__column--social">
				<p class="plp-footer__social-title">Suivez nous sur</p>
				<a class="plp-footer__social-link" href="https://www.facebook.com/provenceliveprod/" target="_blank" rel="noopener noreferrer" aria-label="Facebook Provence Live Prod">
					<svg class="plp-footer__icon plp-footer__icon--facebook" aria-hidden="true" viewBox="0 0 24 24"><path d="M13.7 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.3-1.5 1.6-1.5H17V3.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v2.3H8v3.1h2.6v8h3.1Z" fill="currentColor"/></svg>
				</a>
			</div>
		</div>

		<div class="plp-footer__bottom">
			<div class="ast-container">
				<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> Provence Live Prod. Tous droits réservés.</p>
				<?php if ( get_privacy_policy_url() ) : ?>
					<a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Politique de confidentialité</a>
				<?php endif; ?>
			</div>
		</div>
	</footer>
	</div><!-- #page -->
<?php
astra_body_bottom();
wp_footer();
?>
	</body>
</html>
