<?php
class AC_Page extends Page
{
	function PagePreInclude()
	{
		if( !Login::$logged )
			$this->MarkRedirect()->ThrowReturn();
		
		if( isset($_POST['form_sent']) )
		{
			// Cliente
			$values = array();
			$values['ident_nome'] = $_POST['nome'];
			$values['ident_apelido'] = $_POST['nick'];
			$values['ident_nascimento'] = KudosFunctions::converter_data($_POST['nascimento']);
			$values['ident_sexo'] = $_POST['sexo'];
			$values['cont_telresidencial'] = $_POST['tel_residencial'];
			$values['cont_telcelular'] = $_POST['tel_celular'];
			$values['cont_telcomercial'] = $_POST['tel_comercial'];
			$values['opt_news'] = (int) isset($_POST['news']);
			if( Login::$login_fetch['tipo_pessoa']==ValueConsts::PESSOA_FISICA ) {
				$values['cpf'] = $_POST['cpf'];
			} else {
				$values['cnpj'] = $_POST['cnpj'];
				$values['inscricao_estadual'] = $_POST['inscricao_estadual'];
			}
			db::update(ShopDBConsts::TABLE_CLIENT, $values, array('id_cliente' => Login::$login_fetch['id_cliente']));
			
			// Endereço
			$values = array();
			$values['ender_cep'] = str_replace('-', '', $_POST['cep']);
			$values['ender_estado'] = $_POST['estado'];
			$values['ender_cidade'] = $_POST['cidade'];
			$values['ender_bairro'] = $_POST['bairro'];
			$values['ender_endereco'] = $_POST['endereco'];
			$values['ender_numero'] = $_POST['numero'];
			$values['ender_complemento'] = $_POST['complemento'];
			db::update(ShopDBConsts::TABLE_ADDR, $values, array('id_usuario' => Login::$login_id, 'principal' => 1));
			
			$this->view_chain->success = true;
			$this->MarkRedirectChain();
		} else {
			$client = new ClientFetch(Login::$login_id);
			$this->view_data->ident_fields = $client->fetch;
			$this->view_data->addr_fields = $client->FetchAddr()->addr;
		}
	}
}

$page = Site::LinkIncludePage( new AC_Page() );
$page->view_site->entire_flow = true;
?>

<?php if(!$page->view_chain->success) { ?>
<script type="text/javascript">
$(function() {
	
	// inputs -------------------------------------------------------------------------------------
	$('input[name="cep"]').setMask();
	$('input[name="nascimento"]').setMask({mask: '99/99/9999'});
	$('input[name="cpf"]').setMask();
	$('input[name="cnpj"]').setMask();
	$('input[name="tel_residencial"]').setMask();
	$('input[name="tel_celular"]').setMask();
	$('input[name="tel_comercial"]').setMask();
	
	// validate -----------------------------------------------------------------------------------
	$('#main-form').validate({
		rules: {
			nome: 'required', 
			cpf: {
				cpf: 'valid',
				required: true
			},
			cnpj: {
				required: true
			},
			inscricao_estadual: {
				//required: true
			},
			cep: 'required',
			cidade: 'required',
			estado: 'required',
			endereco: 'required',
			//nick: 'required'
		},
		messages: {
			nome: "Informe o seu nome",
			cpf: "Informe um CPF válido",
			cnpj: "Informe o CNPJ da empresa",
			//ie: "Informe a inscrição estadual",
			//nascimento: "Insira sua data de nascimento",
			
			cep: "Informe o CEP",
			cidade: "Informe a sua cidade",
			endereco: "Informe o endereço",
			
			nick: "Informe como será chamado na loja"
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().parent() );
		}
	});
	
});
</script>
<?php } ?>


<span id="alterar">


<div class="content">
	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_alterar_cadastro.png'); ?>);"></div>
	
	<?php if($page->view_chain->success) { ?>
		<hr class="line"></hr>
		Cadastro alterado com sucesso.
	<?php } else { ?>
		<form method="post" id="main-form" class="model-widgets model-lightform">
		
			<div class="fieldset">
				<h2 class="txt-marine">Identificação</h2>
				<hr class="line"></hr>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Nome completo: <span>*</span></div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="nome" value="<?php echo $page->view_data->ident_fields['ident_nome']; ?>" /></div>
				</label>
				
				
				<?php if( Login::$login_fetch['tipo_pessoa']==ValueConsts::PESSOA_FISICA ) { ?>
					<label class="widget-line">
						<div class="label-joint txt-hardgray">CPF: <span>*</span></div>
						<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="cpf" alt="cpf" value="<?php echo $page->view_data->ident_fields['cpf']; ?>" /></div>
					</label>
				<?php } else { ?>
					<label class="widget-line">
						<div class="label-joint txt-hardgray">CNPJ: <span>*</span></div>
						<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="cnpj" alt="cnpj" value="<?php echo $page->view_data->ident_fields['cnpj']; ?>" /></div>
					</label>
					
					<label class="widget-line">
						<div class="label-joint txt-hardgray">Inscrição estadual:</div>
						<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="inscricao_estadual" value="<?php echo $page->view_data->ident_fields['inscricao_estadual']; ?>" /></div>
					</label>
				<?php } ?>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Sexo:</div>
					<div class="input-joint">
						<input type="radio" name="sexo" value="m" <?php if( $page->view_data->ident_fields['ident_sexo']=='m' ) echo 'checked="checked"'; ?> /> Masculino
						<input type="radio" name="sexo" value="f" <?php if( $page->view_data->ident_fields['ident_sexo']=='f' ) echo 'checked="checked"'; ?> style="margin-left: 12px;" /> Feminino
					</div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Data de nascimento:</div>
					<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="nascimento" value="<?php echo $page->view_data->ident_fields['view_nascimento']; ?>" /></div>
				</label>
			</div>
			
			<div class="fieldset">
				<h2 class="txt-marine">Endereço</h2>
				<hr class="line"></hr>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">CEP: <span>*</span></div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="cep" alt="cep" value="<?php echo $page->view_data->addr_fields['ender_cep']; ?>" /></div>
				</label>
				
				<div class="widget-line">
					<div class="label-joint txt-hardgray">Cidade: <span>*</span></div>
					<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="cidade" value="<?php echo $page->view_data->addr_fields['ender_cidade']; ?>" /></div>
					<div class="label-joint minor txt-hardgray">Estado:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>">
						<select name="estado">
							<?php
							foreach( db::select('cms_estados', '*', null, 'nome') as $row_uf )
								printf('<option value="%s" %s>%s</option>', $row_uf['uf'], $page->view_data->addr_fields['ender_estado']==$row_uf['uf'] ? 'selected="selected"' : '', $row_uf['uf']);
							?>
						</select>
					</div>
				</div>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Endereço: <span>*</span></div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="endereco" value="<?php echo $page->view_data->addr_fields['ender_endereco']; ?>" /></div>
				</label>
				
				<div class="widget-line">
					<div class="label-joint txt-hardgray">Número:</div>
					<div class="input-wrap-round medium <?php echo browser::css(); ?>"><input type="text" name="numero" value="<?php echo $page->view_data->addr_fields['ender_numero']; ?>" /></div>
					<div class="label-joint minor txt-hardgray">Bairro:</div>
					<div class="input-wrap-round medium <?php echo browser::css(); ?>"><input type="text" name="bairro" value="<?php echo $page->view_data->addr_fields['ender_bairro']; ?>" /></div>
				</div>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Complemento:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="complemento" value="<?php echo $page->view_data->addr_fields['ender_complemento']; ?>" /></div>
				</label>
			</div>
			
			<div class="fieldset">
				<h2 class="txt-marine">Contato</h2>
				<hr class="line"></hr>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Telefone residencial:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="tel_residencial" alt="phone" value="<?php echo $page->view_data->ident_fields['cont_telresidencial']; ?>" /></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Telefone celular:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="tel_celular" alt="phone" value="<?php echo $page->view_data->ident_fields['cont_telcelular']; ?>" /></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Telefone comercial:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="tel_comercial" alt="phone" value="<?php echo $page->view_data->ident_fields['cont_telcomercial']; ?>" /></div>
				</label>
				
				<hr class="line"></hr>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray" style="margin-top: 0;">Como gostaria de ser chamado:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="nick" value="<?php echo $page->view_data->ident_fields['ident_apelido']; ?>" /></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray"></div>
					<div class="input-joint">
						<input type="checkbox" name="news" style="float: left; margin-top: 7px; margin-right: 8px;" <?php if($page->view_data->ident_fields['opt_news']==1) echo 'checked="checked"'; ?> />
						Quero assinar o newsletter da loja para receber<br />promoções exclusivas e descontos por e-mail.
					</div>
				</label>
			</div>
			
			<input type="image" src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_alterar.png'); ?>" />
			<input type="hidden" name="form_sent" />
		</form>
	<?php } ?>
</div>


</span>