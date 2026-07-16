<?php
/**
 * Pagina "Contatti" — testi modificabili dall'editor WordPress + modulo e mappa.
 *
 * @package Lamacupa
 */
get_header();
if ( have_posts() ) :
	the_post();
	lamacupa_page_hero( 'Contatti' );
	?>
	<section class="block" style="padding-top:56px">
		<div class="wrap"><div class="contact-grid">
			<div>
				<div class="prose" style="max-width:none"><?php the_content(); ?></div>
				<div class="mapbox"><div class="imgslot" id="contact-map"></div></div>
			</div>
			<div>
				<form onsubmit="event.preventDefault();this.reset();var m=this.querySelector('.okmsg');if(m)m.style.display='block';">
					<div class="field"><label data-en="Name and surname">Nome e cognome</label><input type="text" required placeholder="Mario Rossi" data-en-ph="John Smith" /></div>
					<div class="field"><label>Email</label><input type="email" required placeholder="mario@email.it" data-en-ph="john@email.com" /></div>
					<div class="field"><label data-en="Subject">Oggetto</label><input type="text" placeholder="Informazioni / Ordine / Orci" data-en-ph="Info / Order / Jars" /></div>
					<div class="field"><label data-en="Message">Messaggio</label><textarea required placeholder="Scrivici qui..." data-en-ph="Write to us here..."></textarea></div>
					<button class="btn btn-solid" type="submit" data-en="Send message">Invia messaggio</button>
					<p class="okmsg" style="display:none;margin-top:16px;color:var(--olive-soft);font-size:15px" data-en="Thank you! We will reply as soon as possible.">Grazie! Ti risponderemo al più presto.</p>
				</form>
			</div>
		</div></div>
	</section>
	<?php
endif;
get_footer();
