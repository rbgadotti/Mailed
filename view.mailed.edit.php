<h2 class="title">Editar</h2>
<form action="" method="POST">
	<table class="form-table">
		<!-- Email -->
		<tr>
			<th>Email</th>
			<td>
				<input
					type="email"
					name="<?php echo MAILED_DOMAIN . '_email'; ?>"
					value="<?php echo Mailed::$result[MAILED__FIELD_EMAIL]; ?>"
					class="regular-text"
					readonly
				/>
			</td>
		</tr>
		<!-- Nome -->
		<tr>
			<th>Nome</th>
			<td>
				<input
					type="text"
					name="<?php echo MAILED_DOMAIN . '_firstname'; ?>"
					value="<?php echo Mailed::$result[MAILED__FIELD_FIRSTNAME]; ?>"
					class="regular-text"
				/>
			</td>
		</tr>
		<!-- Sobrenome -->
		<tr>
			<th>Sobrenome</th>
			<td>
				<input
					type="text"
					name="<?php echo MAILED_DOMAIN . '_lastname'; ?>"
					value="<?php echo Mailed::$result[MAILED__FIELD_LASTNAME]; ?>"
					class="regular-text"
				/>
			</td>
		</tr>
		<!-- Status na lista do MailChimp -->
		<?php if(MailedMailchimp::is_mailchimp_intragrate_list_table_on() && false): ?>
		<tr>
			<th>Status na lista do MailChimp</th>
			<td>
				<select name="<?php echo MAILED_DOMAIN . '_mailchimp_status'; ?>">
					<option value="subscribed" <?php echo Mailed::$result['mailchimp_status'] == 'subscribed' ? 'selected': ''; ?>>Subscribed</option>
					<option value="unsubscribed" <?php echo Mailed::$result['mailchimp_status'] == 'unsubscribed' ? 'selected': ''; ?>>Unsubscribed</option>
					<option value="cleaned" <?php echo Mailed::$result['mailchimp_status'] == 'cleaned' ? 'selected': ''; ?>>Cleaned</option>
					<option value="pending" <?php echo Mailed::$result['mailchimp_status'] == 'pending' ? 'selected': ''; ?>>Pending</option>
				</select>
			</td>
		</tr>
		<?php endif; ?>
		<!-- Última edição em -->
		<tr>
			<th>Última edição em</th>
			<td>
				<p><?php echo strtotime(Mailed::$result[MAILED__FIELD_EDITED_AT]) == 0 ? '-' : date('d/m/Y H:i', strtotime(Mailed::$result[MAILED__FIELD_EDITED_AT])); ?></p>
			</td>
		</tr>
		<!-- Criado em -->
		<tr>
			<th>Criado em</th>
			<td>
				<p><?php echo strtotime(Mailed::$result[MAILED__FIELD_CREATED_AT]) == 0 ? '-' : date('d/m/Y H:i', strtotime(Mailed::$result[MAILED__FIELD_CREATED_AT])); ?></p>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>