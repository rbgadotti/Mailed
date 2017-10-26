<div class="wrap mailed--settings">
	<h1>Mailed Newsletter Settings</h1>
	
	<?php if(!empty(get_option(MAILED__SETTINGS_GROUP . '_mailchimp_apikey'))): ?>
		<?php if(!MailedMailchimp::api_key_is_valid()): ?>
		<div class="notice notice-error is-dismissible"> 
			<p><strong>API Key inválida</strong></p>
		</div>
		<?php endif; ?>
	<?php endif; ?>
	<form action="options.php" method="POST">
		<?php

			settings_fields( MAILED__SETTINGS_GROUP );
			do_settings_sections( MAILED__SETTINGS_GROUP );

		?>
		<!-- Configurações da integração com MailChimp -->
		<h2 class="title">Integração com MailChimp</h2>
		<table class="form-table">
			<!-- Mailchimp API Key -->
			<tr>
				<th>API Key</th>
				<td>
					<input
						type="text"
						name="<?php echo MAILED__SETTINGS_GROUP . '_mailchimp_apikey'; ?>"
						value="<?php echo esc_attr( get_option(MAILED__SETTINGS_GROUP . '_mailchimp_apikey') ); ?>"
						class="regular-text"
					/>
					<?php if(!empty(get_option(MAILED__SETTINGS_GROUP . '_mailchimp_apikey'))): ?>
						<?php if(MailedMailchimp::api_key_is_valid()): ?>
						<span class="dashicons dashicons-yes"></span>
						<?php else: ?>
						<span class="dashicons dashicons-no"></span>
						<?php endif; ?>
					<?php endif; ?>
					<p class="description">Pode ser encontrado no menu Account opção API Keys.</p>
				</td>
			</tr>
			<!-- Mailchimp List ID -->
			<tr>
				<th>ID da lista</th>
				<td>
					<input
						type="text"
						name="<?php echo MAILED__SETTINGS_GROUP . '_mailchimp_list_id'; ?>"
						value="<?php echo esc_attr( get_option(MAILED__SETTINGS_GROUP . '_mailchimp_list_id') ); ?>"
						class="regular-text"
					/>
					<p class="description">Pode ser encontrado na página Lists opção Settings.</p>
				</td>
			</tr>
			<!-- Mailchimp Inscrição automática -->
			<tr>
				<th>Inscrição automática</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>Inscrição automática</span></legend>
						<label>
							<input
								name="<?php echo MAILED__SETTINGS_GROUP . '_mailchimp_auto_subscribe'; ?>"
								type="checkbox"
								value="1"
								<?php echo get_option(MAILED__SETTINGS_GROUP . '_mailchimp_auto_subscribe', 0) ? 'checked' : ''; ?>
							/>
							Adiciona o usuário à lista do MailChimp automaticamente ao inscrever-se
						</label>
					</fieldset>
				</td>
			</tr>
			<!-- Mailchimp Lista table integrada -->
			<tr>
				<th>Integração com lista</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>Integração com lista</span></legend>
						<label>
							<input
								name="<?php echo MAILED__SETTINGS_GROUP . '_mailchimp_intragrate_list_table'; ?>"
								type="checkbox"
								value="1"
								<?php echo get_option(MAILED__SETTINGS_GROUP . '_mailchimp_intragrate_list_table', 0) ? 'checked' : ''; ?>
							/>
							Adiciona informações e recursos de interação do MailChimp na listagem de inscritos. 
						</label>
					</fieldset>
				</td>
			</tr>
		</table>
		<!-- Configuração de mensagens -->
		<h2 class="title">Mensagens de retorno do formulário de inscrição</h2>
		<p>As mensagens serão exibidas somente quando o shortcode <kbd>do[teste]</kbd> for inserido.</p>
		<table class="form-table">
			<!-- Ajax Message 200 -->
			<tr>
				<th>Sucesso</th>
				<td>
					<input
						type="text"
						name="<?php echo MAILED__SETTINGS_GROUP . '_ajax_message_200'; ?>"
						value="<?php echo esc_attr( get_option(MAILED__SETTINGS_GROUP . '_ajax_message_200', MAILED__AJAX_ERROR_200) ); ?>"
						class="regular-text"
					/>
				</td>
			</tr>
			<!-- Ajax Message 400 -->
			<tr>
				<th>Dados inválidos</th>
				<td>
					<input
						type="text"
						name="<?php echo MAILED__SETTINGS_GROUP . '_ajax_message_400'; ?>"
						value="<?php echo esc_attr( get_option(MAILED__SETTINGS_GROUP . '_ajax_message_400', MAILED__AJAX_ERROR_400) ); ?>"
						class="regular-text"
					/>
				</td>
			</tr>
		</table>
		<!-- Classes de ações -->
		<h2 class="title">Classes de ações</h2>
		<p>São classes que indicam qual conteúdo será exibido ou ocultado em cada ação do formulário de cadastro.</p>
		<p>
			<kbd>mailed-msg-return</kbd>, <kbd>mailed-msg-success</kbd> e <kbd>mailed-msg-error</kbd><br>
			Recebe a mensagem de retorno, sucesso e erro, respectivamente, do formulário.
		</p>
		<p>
			<kbd>mailed-show-onsubmit</kbd> e <kbd>mailed-hide-onsubmit</kbd><br>
			Exibe/oculta um determinado conteúdo quando o formulário for enviado.
		</p>
		<p>
			<kbd>mailed-show-oncomplete</kbd> e <kbd>mailed-hide-oncomplete</kbd><br>
			Exibe/oculta um determinado conteúdo quando o envio do formulário for finalizado.
		</p>
		<p>
			<kbd>mailed-show-onsuccess</kbd> e <kbd>mailed-hide-onsuccess</kbd><br>
			Exibe/oculta um determinado conteúdo quando o envio do formulário for realizado com sucesso.
		</p>
		<p>
			<kbd>mailed-show-onerror</kbd> e <kbd>mailed-hide-onerror</kbd><br>
			Exibe/oculta um determinado conteúdo quando ocorrer um erro durante o envio do formulário.
		</p>
		<!-- Submit -->
		<table class="form-table">
			<tr>
				<td><?php submit_button(); ?></td>
			</tr>			
		</table>
	</form>
</div>