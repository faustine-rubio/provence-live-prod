<?php
/**
 * Fonctions du thème enfant Provence Live Prod.
 *
 * Les contenus et la mise en page restent gérés dans WordPress/Astra.
 * Ce fichier ne contient que les fonctions spécifiques à PLP.
 *
 * @package PLP_Astra_Child
 */

defined( 'ABSPATH' ) || exit;

/** Charge les feuilles de style du parent et du thème enfant. */
function plp_enqueue_styles() {
	$parent_style = 'astra-theme-css';

	wp_enqueue_style(
		$parent_style,
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	wp_enqueue_style(
		'plp-child-style',
		get_stylesheet_uri(),
		array( $parent_style ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'plp-cinzel-font',
		'https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'plp_enqueue_styles', 20 );

/**
 * Formulaire de demande du catalogue.
 *
 * À insérer dans la page Catalogue avec le shortcode [plp_catalogue_form].
 * Les demandes sont envoyées à l'adresse e-mail d'administration WordPress ;
 * l'équipe PLP peut ensuite répondre manuellement avec le lien du catalogue.
 */
function plp_catalogue_form_shortcode() {
	$status = isset( $_GET['plp_catalogue'] ) ? sanitize_key( wp_unslash( $_GET['plp_catalogue'] ) ) : '';

	ob_start();
	?>
	<section class="plp-catalogue-form" aria-labelledby="plp-catalogue-title">
		<div class="plp-catalogue-form__intro">
			<p class="plp-eyebrow">Catalogue artistes</p>
			<h2 id="plp-catalogue-title">Recevez le lien du catalogue</h2>
			<p>Indiquez vos coordonnées. Notre équipe vous transmettra personnellement le lien du catalogue.</p>
		</div>

		<?php if ( 'success' === $status ) : ?>
			<p class="plp-form-notice plp-form-notice--success" role="status">Merci, votre demande a bien été envoyée. Nous vous répondrons rapidement.</p>
		<?php elseif ( 'error' === $status ) : ?>
			<p class="plp-form-notice plp-form-notice--error" role="alert">Merci de compléter tous les champs obligatoires avant d'envoyer votre demande.</p>
		<?php endif; ?>

		<form class="plp-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="plp_catalogue_request">
			<?php wp_nonce_field( 'plp_catalogue_request', 'plp_catalogue_nonce' ); ?>

			<p>
				<label for="plp-last-name">Nom <span aria-hidden="true">*</span></label>
				<input id="plp-last-name" name="last_name" type="text" autocomplete="family-name" required>
			</p>
			<p>
				<label for="plp-first-name">Prénom <span aria-hidden="true">*</span></label>
				<input id="plp-first-name" name="first_name" type="text" autocomplete="given-name" required>
			</p>
			<p class="plp-form__full-width">
				<label for="plp-email">Adresse e-mail <span aria-hidden="true">*</span></label>
				<input id="plp-email" name="email" type="email" autocomplete="email" required>
			</p>
			<p class="plp-form__full-width"><button type="submit" class="button">Demander le catalogue</button></p>
		</form>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'plp_catalogue_form', 'plp_catalogue_form_shortcode' );

/** Traite et transmet une demande de catalogue. */
function plp_handle_catalogue_request() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if ( ! isset( $_POST['plp_catalogue_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['plp_catalogue_nonce'] ) ), 'plp_catalogue_request' ) ) {
		wp_safe_redirect( add_query_arg( 'plp_catalogue', 'error', $redirect ) );
		exit;
	}

	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( '' === $first_name || '' === $last_name || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'plp_catalogue', 'error', $redirect ) );
		exit;
	}

	$subject = sprintf( '[%s] Demande de catalogue', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$message = "Nouvelle demande de catalogue :\n\n";
	$message .= 'Prénom : ' . $first_name . "\n";
	$message .= 'Nom : ' . $last_name . "\n";
	$message .= 'E-mail : ' . $email . "\n";
	$message .= "\nRépondez à cette personne pour lui transmettre le lien du catalogue.";

	wp_mail( get_option( 'admin_email' ), $subject, $message, array( 'Reply-To: ' . $email ) );

	wp_safe_redirect( add_query_arg( 'plp_catalogue', 'success', $redirect ) );
	exit;
}
add_action( 'admin_post_nopriv_plp_catalogue_request', 'plp_handle_catalogue_request' );
add_action( 'admin_post_plp_catalogue_request', 'plp_handle_catalogue_request' );

/**
 * Crée la page de demande de catalogue une seule fois.
 *
 * La vérification dans l'administration permet aussi de créer la page si le
 * thème enfant était déjà actif lors de l'ajout de cette fonctionnalité.
 */
function plp_create_catalogue_page() {
	$slug = 'demande-de-catalogue';

	if ( get_page_by_path( $slug ) ) {
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Demande de catalogue',
			'post_name'    => $slug,
			'post_content' => '<!-- wp:shortcode -->[plp_catalogue_form]<!-- /wp:shortcode -->',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);

	if ( ! is_wp_error( $page_id ) ) {
		update_option( 'plp_catalogue_page_id', (int) $page_id, false );
	}
}
add_action( 'after_switch_theme', 'plp_create_catalogue_page' );
add_action( 'init', 'plp_create_catalogue_page', 20 );

/**
 * Retourne le carrousel automatique des artistes et spectacles.
 *
 * Les images sont lues directement dans assets/images/carousel : il suffit
 * donc d'ajouter un visuel Ã  ce dossier pour qu'il apparaisse dans la page.
 */
function plp_artistes_spectacles_carousel_shortcode() {
	$image_files = glob( get_stylesheet_directory() . '/assets/images/carousel/*.{jpg,jpeg,png,webp,avif}', GLOB_BRACE );

	if ( empty( $image_files ) ) {
		return '';
	}

	sort( $image_files, SORT_NATURAL | SORT_FLAG_CASE );
	$base_url = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/images/carousel/';

	ob_start();
	?>
	<section class="plp-artists-carousel" aria-label="Artistes et spectacles">
		<div class="plp-artists-carousel__viewport">
			<div class="plp-artists-carousel__track">
				<?php for ( $set = 0; $set < 2; $set++ ) : ?>
					<?php foreach ( $image_files as $image_index => $image_file ) : ?>
						<?php $filename = basename( $image_file ); ?>
						<figure class="plp-artists-carousel__slide" tabindex="<?php echo 0 === $set ? '0' : '-1'; ?>"<?php echo 1 === $set ? ' aria-hidden="true"' : ''; ?>>
							<img src="<?php echo esc_url( $base_url . rawurlencode( $filename ) ); ?>" alt="" loading="<?php echo 0 === $set ? 'eager' : 'lazy'; ?>" decoding="async">
							<figcaption class="plp-artists-carousel__caption">
								<?php echo esc_html( sprintf( 'photo %d', $image_index + 1 ) ); ?>
							</figcaption>
						</figure>
					<?php endforeach; ?>
				<?php endfor; ?>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'plp_artistes_spectacles_carousel', 'plp_artistes_spectacles_carousel_shortcode' );

/** CrÃ©e la page Â« Artistes et spectacles Â» une seule fois. */
function plp_create_artistes_spectacles_page() {
	$slug = 'artistes-et-specatcles';

	$existing_page = get_page_by_path( $slug );
	if ( $existing_page ) {
		// Corrige l'intitulÃ© de la page crÃ©Ã©e avec la premiÃ¨re version.
		if ( 'Artistes et Specatcles' === $existing_page->post_title ) {
			wp_update_post(
				array(
					'ID'         => $existing_page->ID,
					'post_title' => 'Artistes et spectacles',
				)
			);
		}

		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Artistes et spectacles',
			'post_name'    => $slug,
			'post_content' => '<!-- wp:html -->[plp_artistes_spectacles_carousel]<!-- /wp:html -->',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);

	if ( ! is_wp_error( $page_id ) ) {
		update_option( 'plp_artistes_spectacles_page_id', (int) $page_id, false );
	}
}
add_action( 'after_switch_theme', 'plp_create_artistes_spectacles_page' );
add_action( 'init', 'plp_create_artistes_spectacles_page', 20 );

/**
 * Crée la page « Accueil test » une seule fois.
 *
 * Contient : le titre "Au service du spectacle vivant" en gris, centré et
 * réduit, la vidéo de présentation en lecture automatique (sans son), puis
 * une bande grise pleine largeur "Nos services" avec 5 services présentés
 * (icône check, titre en gras, descriptif). Sert à tester une mise en page
 * avant de l'appliquer à la vraie page d'accueil.
 *
 * Déposez le fichier vidéo dans :
 * wp-content/themes/votre-theme-enfant/assets/video/video-logo-plp.mp4
 */
function plp_create_test_homepage_page() {
	$slug = 'accueil-test';

	$existing_page = get_page_by_path( $slug );
	if ( $existing_page ) {
		plp_add_strengths_to_test_homepage( $existing_page );
		return;
	}

	$video_url = esc_url( get_stylesheet_directory_uri() . '/assets/video/video-logo-plp.mp4' );

	$icon = '<svg class="plp-service-icon" viewBox="0 0 24 24" width="42" height="42" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#6b6ca4"/><path d="M7 12.5l3 3 7-7" stroke="#ffffff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>';

	$services = array(
		array(
			'title' => 'Production de spectacle',
			'text'  => 'Des plus originaux aux plus grands shows',
		),
		array(
			'title' => "Catalogue d'artistes",
			'text'  => 'Des talents locaux : du solo jusqu\'à l\'orchestre',
		),
		array(
			'title' => 'Facilités administratives',
			'text'  => 'Contrat de cession, déclarations et facture unique',
		),
		array(
			'title' => 'Interlocuteur unique',
			'text'  => 'Du choix du spectacle jusqu\'au jour de la présentation',
		),
		array(
			'title' => 'Booking et promotion',
			'text'  => 'Diffusion et promotion des artistes auprès des organisateurs (collectivité, comité des fêtes, entreprises, particuliers,...)',
		),
	);

	$services_html = '<div class="plp-services-grid">';
	foreach ( $services as $service ) {
		$services_html .= '<div class="plp-service-item">';
		$services_html .= $icon;
		$services_html .= '<h3 class="plp-service-title">' . esc_html( $service['title'] ) . '</h3>';
		$services_html .= '<p class="plp-service-text">' . esc_html( $service['text'] ) . '</p>';
		$services_html .= '</div>';
	}
	$services_html .= '</div>';

	$content  = '<!-- wp:heading {"textAlign":"center","level":1,"style":{"color":{"text":"#a6a6a6"},"typography":{"fontSize":"2rem"}}} -->' . "\n";
	$content .= '<h1 class="wp-block-heading has-text-align-center" style="color:#a6a6a6;font-size:2rem;text-align:center">Au service du spectacle vivant</h1>' . "\n";
	$content .= '<!-- /wp:heading -->' . "\n\n";

	$content .= '<!-- wp:video {"autoplay":true,"loop":true,"muted":true,"playsInline":true,"controls":false} -->' . "\n";
	$content .= '<figure class="wp-block-video"><video autoplay loop muted playsinline src="' . $video_url . '"></video></figure>' . "\n";
	$content .= '<!-- /wp:video -->' . "\n\n";

	$content .= '<!-- wp:group {"align":"full","style":{"color":{"background":"#e5e5e5"}},"layout":{"type":"constrained"}} -->' . "\n";
	$content .= '<div class="wp-block-group alignfull has-background" style="background-color:#e5e5e5">' . "\n";
	$content .= '<!-- wp:heading {"textAlign":"center"} -->' . "\n";
	$content .= '<h2 class="wp-block-heading has-text-align-center">Nos services</h2>' . "\n";
	$content .= '<!-- /wp:heading -->' . "\n\n";
	$content .= '<!-- wp:html -->' . "\n";
	$content .= $services_html . "\n";
	$content .= '<!-- /wp:html -->' . "\n";
	$content .= '</div>' . "\n";
	$content .= '<!-- /wp:group -->' . "\n\n";
	$content .= plp_test_homepage_strengths_section() . "\n\n";
	$content .= plp_test_homepage_following_sections();

	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Accueil test',
			'post_name'    => $slug,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);

	if ( ! is_wp_error( $page_id ) ) {
		update_option( 'plp_test_homepage_page_id', (int) $page_id, false );
	}
}
add_action( 'after_switch_theme', 'plp_create_test_homepage_page' );
add_action( 'init', 'plp_create_test_homepage_page', 20 );

/** Retourne la bande pleine largeur des points forts pour Accueil test. */
function plp_test_homepage_strengths_section() {
	$strengths = array(
		array( 'Réactivité', '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="13" r="7"/><path d="M12 9v4l3 2M9 2h6M12 6V3M19 6l1.5-1.5M5 6 3.5 4.5"/></svg>' ),
		array( 'Rigueur', '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 19 6v5c0 4.5-3 7.8-7 10-4-2.2-7-5.5-7-10V6l7-3Z"/><path d="m8.5 12 2.2 2.2 4.8-5"/></svg>' ),
		array( 'Adaptabilité', '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a7 7 0 0 0-12-1L4 9M6 16a7 7 0 0 0 12 1l2-2M4 9h5V4M20 15h-5v5"/></svg>' ),
		array( 'Proximité', '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.2"/><path d="M3 20c.5-4 2.6-6 6-6s5.5 2 6 6M14 15c3.5 0 5.7 1.7 6 5"/></svg>' ),
	);

	$html  = '<!-- wp:group {"align":"full","className":"plp-home-strengths plp-home-strengths--v2","layout":{"type":"constrained"}} -->' . "\n";
	$html .= '<section class="wp-block-group alignfull plp-home-strengths plp-home-strengths--v2">' . "\n";
	$html .= '<!-- wp:heading {"textAlign":"center"} -->' . "\n";
	$html .= '<h2 class="wp-block-heading has-text-align-center">Nos points forts</h2>' . "\n";
	$html .= '<!-- /wp:heading -->' . "\n";
	$html .= '<div class="plp-home-strengths__grid">';

	foreach ( $strengths as $strength ) {
		$html .= '<div class="plp-home-strengths__item">' . $strength[1] . '<h3>' . esc_html( $strength[0] ) . '</h3></div>';
	}

	$html .= '</div>' . "\n";
	$html .= '</section>' . "\n";
	$html .= '<!-- /wp:group -->';

	return $html;
}

/** Retourne les deux bandes qui suivent les points forts d'Accueil test. */
function plp_test_homepage_following_sections() {
	$html  = '<!-- wp:group {"align":"full","className":"plp-home-trust","layout":{"type":"constrained"}} -->' . "\n";
	$html .= '<section class="wp-block-group alignfull plp-home-band plp-home-trust">' . "\n";
	$html .= '<h2 class="wp-block-heading has-text-align-center">Ils nous ont fait confiance</h2>' . "\n";
	$html .= '</section>' . "\n";
	$html .= '<!-- /wp:group -->' . "\n\n";
	$html .= plp_test_homepage_stats_section();

	return $html;
}

/** Retourne la bande des chiffres-clés pour la page Accueil test. */
function plp_test_homepage_stats_section() {
	$html  = '<!-- wp:group {"align":"full","className":"plp-home-stats plp-home-stats--v4","layout":{"type":"constrained"}} -->' . "\n";
	$html .= '<section class="wp-block-group alignfull plp-home-band plp-home-stats plp-home-stats--v4">' . "\n";
	$html .= '<h2 class="wp-block-heading has-text-align-center">Provence Live Prod en quelques chiffres</h2>' . "\n";
	$html .= '<div class="plp-home-stats__grid">';
	$html .= '<div class="plp-home-stats__item"><strong>Septembre 2024</strong><p>Lancement de Provence Live Prod</p></div>';
	$html .= '<div class="plp-home-stats__item"><strong>PLATESV-D-2024-005143</strong><p>Notre numéro de licence d’entrepreneur du spectacle vivant</p></div>';
	$html .= '<div class="plp-home-stats__item"><strong>Plus de 100</strong><p>Provence Live Prod a réalisé plus de 100 prestations en moins de deux ans</p></div>';
	$html .= '</div>' . "\n";
	$html .= '</section>' . "\n";
	$html .= '<!-- /wp:group -->';

	return $html;
}

/** Ajoute la bande aux pages Accueil test déjà créées, une seule fois. */
function plp_add_strengths_to_test_homepage( $page ) {
	$content = $page->post_content;
	$changed = false;
	$updated_content = str_replace(
		array(
			'Sérénité administrative',
			'video-logo-plp-presente.mp4',
		),
		array(
			'Facilités administratives',
			'video-logo-plp.mp4',
		),
		$content
	);

	if ( $updated_content !== $content ) {
		$content = $updated_content;
		$changed = true;
	}

	if ( false === strpos( $content, 'plp-home-strengths--v2' ) ) {
		$replacement = "\n\n" . plp_test_homepage_strengths_section();
		$content     = preg_replace(
			'/\s*<!-- wp:group \\{[^\n]*"plp-home-strengths"[^\n]*\\} -->.*?<!-- \/wp:group -->/s',
			$replacement,
			$content,
			1,
			$replacements
		);

		if ( 0 === $replacements ) {
			$content .= $replacement;
		}

		$changed = true;
	}

	if ( false === strpos( $content, 'plp-home-trust' ) ) {
		$content .= "\n\n" . plp_test_homepage_following_sections();
		$changed = true;
	} elseif ( false === strpos( $content, 'plp-home-stats--v4' ) ) {
		$content = preg_replace(
			'/\s*<!-- wp:group \{[^\n]*plp-home-stats[^\n]*\} -->.*?<!-- \/wp:group -->/s',
			'',
			$content
		);
		$content .= "\n\n" . plp_test_homepage_stats_section();
		$changed = true;
	}

	if ( ! $changed ) {
		return;
	}

	wp_update_post(
		array(
			'ID'           => $page->ID,
			'post_content' => $content,
		)
	);
}

/** Retourne l'URL WordPress d'une page, quels que soient les permaliens. */
function plp_page_url( $slug ) {
	$page = get_page_by_path( $slug );

	if ( $page ) {
		return get_permalink( $page );
	}

	return home_url( '/?pagename=' . $slug );
}

/** Ajoute une classe ciblée au design de la page Catalogue et de la page Accueil test. */
function plp_catalogue_page_body_class( $classes ) {
	if ( is_page( 'demande-de-catalogue' ) ) {
		$classes[] = 'plp-catalogue-page';
	}

	if ( is_page( 'accueil-test' ) ) {
		$classes[] = 'plp-hide-page-title';
	}

	if ( is_page( 'artistes-et-specatcles' ) ) {
		$classes[] = 'plp-artistes-spectacles-page';
	}

	return $classes;
}
add_filter( 'body_class', 'plp_catalogue_page_body_class' );

/** Ajoute Catalogue après Contact dans les menus d'en-tête Astra. */
function plp_add_catalogue_to_header_menu( $items, $args ) {
	$is_primary_menu = isset( $args->theme_location ) && 'primary' === $args->theme_location;
	$is_header_menu  = isset( $args->menu_class ) && false !== strpos( $args->menu_class, 'main-header-menu' );

	if ( ( ! $is_primary_menu && ! $is_header_menu ) || false !== strpos( $items, 'plp-catalogue-menu-item' ) ) {
		return $items;
	}

	$catalogue_item = sprintf(
		'<li class="menu-item plp-catalogue-menu-item"><a href="%1$s" class="menu-link">%2$s</a></li>',
		esc_url( plp_page_url( 'demande-de-catalogue' ) ),
		esc_html__( 'Catalogue', 'plp-astra-child' )
	);

	$updated_items = preg_replace(
		'/(<li\b[^>]*>\s*<a\b[^>]*>\s*Contact\s*<\/a>\s*<\/li>)/u',
		'$1' . $catalogue_item,
		$items,
		1
	);

	return $updated_items ? $updated_items : $items . $catalogue_item;
}
/** Conserve uniquement Accueil et Artistes et Spectacles dans le menu d'en-tÃªte. */
function plp_add_artistes_spectacles_to_header_menu( $items, $args ) {
	$is_primary_menu = isset( $args->theme_location ) && 'primary' === $args->theme_location;
	$is_header_menu  = isset( $args->menu_class ) && false !== strpos( $args->menu_class, 'main-header-menu' );

	if ( ! $is_primary_menu && ! $is_header_menu ) {
		return $items;
	}

	$home_item = sprintf(
		'<li class="menu-item plp-home-menu-item%1$s"><a href="%2$s" class="menu-link"%3$s>Accueil</a></li>',
		is_front_page() ? ' current-menu-item' : '',
		esc_url( home_url( '/' ) ),
		is_front_page() ? ' aria-current="page"' : ''
	);

	$artists_item = sprintf(
		'<li class="menu-item plp-artistes-spectacles-menu-item%1$s"><a href="%2$s" class="menu-link"%3$s>Artistes et Spectacles</a></li>',
		is_page( 'artistes-et-specatcles' ) ? ' current-menu-item' : '',
		esc_url( plp_page_url( 'artistes-et-specatcles' ) ),
		is_page( 'artistes-et-specatcles' ) ? ' aria-current="page"' : ''
	);

	return $home_item . $artists_item;
}
add_filter( 'wp_nav_menu_items', 'plp_add_artistes_spectacles_to_header_menu', 10, 2 );

/** Configure le bouton latÃ©ral du constructeur d'en-tÃªte Astra. */
function plp_header_contact_button_text( $text ) {
	return 'Nous contacter';
}
add_filter( 'astra_get_option_header-button-text', 'plp_header_contact_button_text' );

/** Dirige le bouton latÃ©ral du constructeur d'en-tÃªte Astra vers Contact. */
function plp_header_contact_button_link( $link ) {
	return plp_page_url( 'contact' );
}
add_filter( 'astra_get_option_header-button-link', 'plp_header_contact_button_link' );
