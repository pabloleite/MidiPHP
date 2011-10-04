<?php
class CA_Page extends Page
{
	function PagePreInclude()
	{
		if( !$this->view_chain->login_register_pass )
			$this->MarkRedirect('?p=login')->ThrowReturn();
		
		if( isset($_POST['form_sent']) )
		{
			$email = $this->view_chain->register_email;
			db::select( ShopDBConsts::TABLE_USER, '*', array('login_email' => db::escape($email)) );
			
			if( db::affected() > 0 )
				$this->MarkRedirect('?p=login')->ThrowReturn();
			
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
			if( $_POST['tipo_pessoa']==ValueConsts::PESSOA_FISICA ) {
				$values['cpf'] = $_POST['cpf'];
			} else {
				$values['cnpj'] = $_POST['cnpj'];
				$values['inscricao_estadual'] = $_POST['inscricao_estadual'];
			}
			$client_id = db::insert(ShopDBConsts::TABLE_CLIENT, $values);
			
			// Usuário
			$values = array();
			$values['login_email'] = $email;
			$values['login_senha'] = sha1($_POST['senha']);
			$values['login_ativo'] = 1;
			$values['login_joined'] = 'NOW()';
			$values['cliente_tipo'] = $_POST['tipo_pessoa'];
			$values['id_cliente'] = $client_id;
			$user_id = db::insert(ShopDBConsts::TABLE_USER, $values);
			
			// Endereço
			$values = array();
			$values['id_usuario'] = $user_id;
			$values['principal'] = 1;
			$values['ender_cep'] = str_replace('-', '', $_POST['cep']);
			$values['ender_estado'] = $_POST['estado'];
			$values['ender_cidade'] = $_POST['cidade'];
			$values['ender_bairro'] = $_POST['bairro'];
			$values['ender_endereco'] = $_POST['endereco'];
			$values['ender_numero'] = $_POST['numero'];
			$values['ender_complemento'] = $_POST['complemento'];
			db::insert(ShopDBConsts::TABLE_ADDR, $values);
			
			$this->view_chain->success = true;
			$this->MarkRedirectChain();
		} elseif( !$this->view_chain->success ) {
			$cep = $this->view_chain->register_cep;
			if( $res_data = KudosFunctions::WebRequestAddr($cep) )
			{
				$this->view_data->addr_fields['estado'] = utf8_encode($res_data['uf']);
				$this->view_data->addr_fields['cidade'] = utf8_encode($res_data['cidade']);
				$this->view_data->addr_fields['bairro'] = utf8_encode($res_data['bairro']);
				$this->view_data->addr_fields['endereco'] = utf8_encode($res_data['tipo_logradouro'].' '.$res_data['logradouro']);
			}
			$this->view_data->addr_fields['cep'] = $cep;
			$this->ChainRetain();
		}
	}
}

$page = Site::LinkIncludePage( new CA_Page() );
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
				required: "input[name='tipo_pessoa'][value='0']:checked"
			},
			cnpj: {
				required: "input[name='tipo_pessoa'][value='1']:checked"
			},
			inscricao_estadual: {
				//required: "input[name='tipo_pessoa'][value='1']:checked"
			},
			
			cep: 'required',
			cidade: 'required',
			estado: 'required',
			endereco: 'required',
			
			//nick: 'required',
			senha: {
				required: true,
				minlength: <?php echo G_BUILDDEBUG ? '0' : '6'; ?>
			},
			repeat: {
				required: true,
				equalTo: 'input[name="senha"]'
			},
			politica: 'required'
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
			
			nick: "Informe como será chamado na loja",
			senha: "Informe uma senha com no mínimo 6 caracteres",
			repeat: "Repita a senha",
			politica: "Você deve aceitar a política do site"
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().parent() );
		}
	});
	
	$("input:radio[name='tipo_pessoa']").click(function() {
		if( $(this).val()==<?php echo ValueConsts::PESSOA_FISICA; ?> ) {
			$("#form-fisica").show();
			$("#form-juridica").hide();
		} else {
			$("#form-fisica").hide();
			$("#form-juridica").show();
		}
	});
	
});
</script>
<?php } ?>


<span id="alterar">


<div class="content">
	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_cadastro.png'); ?>);"></div>
	
	
	<?php if($page->view_chain->success) { ?>
		<hr class="line"></hr>
		Cadastro realizado com sucesso!<br /><br />
		Seu e-mail e senha já estão liberados para realizar compras na loja.
	<?php } else { ?>
		<form method="post" id="main-form" class="model-widgets model-lightform">
			<div class="fieldset">
				<h2 class="txt-marine">Identificação</h2>
				<hr class="line"></hr>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Nome completo: <span>*</span></div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="nome" /></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Tipo de pessoa:</div>
					<div class="input-joint">
						<input type="radio" name="tipo_pessoa" value="0" checked="checked" /> Pessoa física
						<input type="radio" name="tipo_pessoa" value="1" style="margin-left: 12px;" /> Pessoa jurídica
					</div>
				</label>
				
				<div id="form-fisica">
					<label class="widget-line">
						<div class="label-joint txt-hardgray">CPF: <span>*</span></div>
						<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="cpf" alt="cpf" /></div>
					</label>
				</div>
				
				<div id="form-juridica" style="display: none;">
					<label class="widget-line">
						<div class="label-joint txt-hardgray">CNPJ: <span>*</span></div>
						<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="cnpj" alt="cnpj" /></div>
					</label>
					
					<label class="widget-line">
						<div class="label-joint txt-hardgray">Inscrição estadual:</div>
						<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="inscricao_estadual" /></div>
					</label>
				</div>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Sexo:</div>
					<div class="input-joint">
						<input type="radio" name="sexo" value="m" checked="checked" /> Masculino
						<input type="radio" name="sexo" value="f" style="margin-left: 12px;" /> Feminino
					</div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Data de nascimento:</div>
					<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="nascimento" /></div>
				</label>
			</div>
			
			<div class="fieldset">
				<h2 class="txt-marine">Endereço</h2>
				<hr class="line"></hr>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">CEP: <span>*</span></div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="cep" alt="cep" value="<?php echo $page->view_data->addr_fields['cep']; ?>" /></div>
				</label>
				
				<div class="widget-line">
					<div class="label-joint txt-hardgray">Cidade: <span>*</span></div>
					<div class="input-wrap-round lesser <?php echo browser::css(); ?>"><input type="text" name="cidade" value="<?php echo $page->view_data->addr_fields['cidade']; ?>" /></div>
					<div class="label-joint minor txt-hardgray">Estado:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>">
						<select name="estado">
							<?php
							foreach( db::select('cms_estados', '*', null, 'nome') as $row_uf )
								printf('<option value="%s" %s>%s</option>', $row_uf['uf'], $page->view_data->addr_fields['estado']==$row_uf['uf'] ? 'selected="selected"' : '', $row_uf['uf']);
							?>
						</select>
					</div>
				</div>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Endereço: <span>*</span></div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="endereco" value="<?php echo $page->view_data->addr_fields['endereco']; ?>" /></div>
				</label>
				
				<div class="widget-line">
					<div class="label-joint txt-hardgray">Número:</div>
					<div class="input-wrap-round medium <?php echo browser::css(); ?>"><input type="text" name="numero" /></div>
					<div class="label-joint minor txt-hardgray">Bairro:</div>
					<div class="input-wrap-round medium <?php echo browser::css(); ?>"><input type="text" name="bairro" value="<?php echo $page->view_data->addr_fields['bairro']; ?>" /></div>
				</div>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Complemento:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="complemento" /></div>
				</label>
			</div>
			
			<div class="fieldset">
				<h2 class="txt-marine">Contato</h2>
				<hr class="line"></hr>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Telefone residencial:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="tel_residencial" alt="phone" /></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Telefone celular:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="tel_celular" alt="phone" /></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Telefone comercial:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="tel_comercial" alt="phone" /></div>
				</label>
				
				<hr class="line"></hr>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">E-mail:</div>
					<div class="input-joint"><strong><?php echo $page->view_chain->register_email ?></strong></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray" style="margin-top: -3px;">Como gostaria de ser chamado:</div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="nick" /></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Senha: <span>*</span></div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="password" name="senha" /></div>
				</label>
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray">Confirme a senha: <span>*</span></div>
					<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="password" name="repeat" /></div>
				</label>
				
				<!--<label class="widget-line">
					<div class="label-joint txt-hardgray"></div>
					<div class="input-joint" style="padding-top: 6px;">
						<input type="checkbox" name="politica" style="float: left; margin-top: 1px; margin-right: 8px;" />
						<strong>Compreendo e aceito a <span class="txt-marine"><a href="?p=pagina&n=politica" target="_blank">Política do Site</a></span> <span>*</span></strong>
					</div>
				</label>-->
				
				<label class="widget-line">
					<div class="label-joint txt-hardgray"></div>
					<div class="input-joint">
						<input type="checkbox" name="news" style="float: left; margin-top: 7px; margin-right: 8px;" />
						Quero assinar o newsletter da loja para receber<br />promoções exclusivas e descontos por e-mail.
					</div>
				</label>
			</div>
			
			<input type="image" src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_criar.png'); ?>" />
			<input type="hidden" name="form_sent" />
		</form>
	<?php } ?>
</div>


</span>