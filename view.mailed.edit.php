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
		<tr>
			<th>Status na lista do MailChimp</th>
			<td>
				<select>
					<option value="">Subscribed</option>
					<option value="">Unsubscribed</option>
					<option value="">Cleaned</option>
					<option value="">Pending</option>
				</select>
			</td>
		</tr>
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