<?php $mailed_obj = new Mailed_List(); ?>
<style>
	
	.mailed-action-buttons {
		display: table;
		float: left;
		margin-bottom: 10px;
	}

	.mailed-action-buttons .button {
		margin-right: 0;
	}

	.mailed-action-buttons .button + .button {
		margin-left: 8px;
	}

	/* Mailchimp integration */

	.mailchimp_status__subscribed,
	.mailchimp_status__unsubscribed,
	.mailchimp_status__cleaned,
	.mailchimp_status__pending {
		display: table;
		padding: 1px 8px;
		border-radius: 10px;
		font-size: 12px;
		font-weight: bold;
		color: #ffffff;
	}

	.mailchimp_status__subscribed {
		background-color: #2ecc71;
	}
	.mailchimp_status__unsubscribed {
		background-color: #ecf0f1;
		color: #95a5a6;
	}
	.mailchimp_status__cleaned {
		background-color: #34495e;
	}
	.mailchimp_status__pending {
		background-color: #f39c12;
	}

</style>
<div class="wrap">
	<h1>Mailed Newsletter</h1>
	<br>
	<div class="clear"></div>
	<div class="mailed-action-buttons">
		<a href="?action=export_csv&page=list" class="button action">Exportar CSV</a>
		<a href="?action=export_excel&page=list" class="button action">Exportar Excel</a>
		<?php if(MailedMailchimp::is_mailchimp_intragrate_list_table_on()): ?>
		<a href="?action=mailchimp_sync&page=list" class="button action">Sincronizar com MailChimp</a>
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