<?php $mailed_obj = new Mailed_List(); ?>
<div class="wrap">
	<h1>Mailed Newsletter</h1>
	<br>
	<div class="clear"></div>
	<div class="mailed-action-buttons">
		<a href="?action=export_csv&page=list" class="button action">Exportar CSV</a>
		<a href="?action=export_excel&page=list" class="button action">Exportar Excel</a>
		<?php if(MailedMailchimp::is_mailchimp_intragrate_list_table_on()): ?>
		<a href="?action=sync_mailchimp&page=list" class="button action">Sincronizar com MailChimp</a>
		<?php endif; ?>
	</div>
	<!-- <div class="clear"></div> -->
	<?php $mailed_obj->prepare_items(); ?>
  <form method="post">
    <input type="hidden" name="page" value="">
    <?php
    	$mailed_obj->search_box( 'Pesquisar', 'search_term' );
  		$mailed_obj->display();
  	?>
	</form>
	<p><small>As informações contidas nesta lista foram cadastradas a partir do formulário configurado no Wordpress e não necessariamente possuem todos os registros contidos na lista do MailChimp.</small></p>
</div>