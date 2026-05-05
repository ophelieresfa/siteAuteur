<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reset_image = '/wp-content/uploads/2026/04/ChatGPT-Image-13-avr.-2026-22_12_29.png';
?>

<div class="sa-reset-page">

	<div class="sa-reset-hero">
		<div class="sa-reset-hero__text">
			<h1>Mot de passe oublié ?</h1>
			<p>
				Réinitialise ton mot de passe SiteAuteur en renseignant l’adresse e-mail associée à ton compte.
				Nous t’enverrons un lien sécurisé pour créer un nouveau mot de passe et retrouver l’accès à ton espace client auteur.
			</p>
		</div>

		<div class="sa-reset-hero__visual">
			<img src="<?php echo esc_url( $reset_image ); ?>" alt="Réinitialisation du mot de passe SiteAuteur">
		</div>
	</div>

	<div class="sa-reset-card-wrap">
		<div class="sa-reset-card">

			<?php if ( isset( $_GET['updated'] ) && 'checkemail' === sanitize_key( $_GET['updated'] ) ) : ?>

				<div class="sa-reset-message sa-reset-message--success">
					Si un compte SiteAuteur est associé à cette adresse e-mail, un e-mail de réinitialisation vient d’être envoyé.
					Pense à vérifier ta boîte de réception ainsi que tes courriers indésirables.
				</div>

				<div class="sa-reset-back">
					<a href="<?php echo esc_url( home_url('/login/') ); ?>">← Retour à la connexion</a>
				</div>

			<?php elseif ( isset( $_GET['updated'] ) && 'password_changed' === sanitize_key( $_GET['updated'] ) ) : ?>

				<div class="sa-reset-message sa-reset-message--success">
					Ton mot de passe a bien été modifié. Tu peux maintenant te reconnecter à ton compte SiteAuteur.
				</div>

				<div class="sa-reset-back">
					<a href="<?php echo esc_url( home_url('/login/') ); ?>">← Retour à la connexion</a>
				</div>

			<?php else : ?>

				<div class="sa-reset-intro">
					<div class="sa-reset-icon-circle">
						<svg viewBox="0 0 24 24" aria-hidden="true">
							<path d="M3 6.75A1.75 1.75 0 0 1 4.75 5h14.5A1.75 1.75 0 0 1 21 6.75v10.5A1.75 1.75 0 0 1 19.25 19H4.75A1.75 1.75 0 0 1 3 17.25V6.75Z" />
							<path d="M4 7l8 6 8-6" />
						</svg>
					</div>
				    <h2>Saisis l’adresse e-mail de ton compte</h2>
                	<p>pour recevoir ton lien de réinitialisation.</p>
				</div>

				<form method="post" action="" class="sa-reset-form">
					<?php wp_nonce_field('sa_forgot_password_action', 'sa_forgot_password_nonce'); ?>
					<input type="hidden" name="sa_forgot_password_submit" value="1">

					<div class="sa-reset-field">
						<label class="screen-reader-text" for="username_b">Adresse e-mail</label>
						<input
							type="email"
							name="username_b"
							id="username_b"
							placeholder="Saisis ton adresse e-mail"
							value="<?php echo isset( $_REQUEST['username_b'] ) ? esc_attr( wp_unslash( $_REQUEST['username_b'] ) ) : ''; ?>"
							required
						>
					</div>

					<div class="sa-reset-actions">
						<button type="submit" class="sa-reset-submit">
							Réinitialiser le mot de passe
						</button>

						<div class="sa-reset-back">
							<a href="<?php echo esc_url( home_url('/login/') ); ?>">← Retour à la connexion</a>
						</div>
					</div>
				</form>

			<?php endif; ?>

		</div>
	</div>

</div>