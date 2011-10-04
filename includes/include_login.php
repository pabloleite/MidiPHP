<?php
class LG_Page extends Page
{
	function PagePreInclude()
	{
		if( isset($_POST['form_sent']) )
		{
			if( $_POST['cad']=='join' )
			{
				$email = trim($_POST['email']);
				db::select( ShopDBConsts::TABLE_USER, '*', array('login_email' => db::escape($email)) );
				
				if( db::affected() > 0 )
					$this->view_data->email_registered = true;
				else {
					$this->view_chain->login_register_pass = true;
					$this->view_chain->register_email = $email;
					$this->view_chain->register_cep = $_POST['cep'];
					$this->MarkRedirectChain('cadastro');
				}
			}
			
			if( $_POST['cad']=='access' )
			{
				$loginOK = Login::$glogin->ManualLogin();
				if( !$loginOK )
					$this->view_data->login_fail = true;
				else {
					// TO-DO: trigger checkout login event 
					$this->MarkRedirect();
				}
			}
		}
	}
}

$page = Site::LinkIncludePage( new LG_Page() );
$page->view_site->entire_flow = true;
?>

<script type="text/javascript">
$(function() {
	var submit_attempted = false;
	
	// inputs -------------------------------------------------------------------------------------
	$('input[name="email"]').focus();
	$('input[name="cad"]').change(function() {
		if(submit_attempted)
			$('#main-form').valid();
	});
	$('input[name="cep"]').focus(function() {
		$('input[name="cad"][value="join"]').attr('checked', 'checked').change();
	});
	$('input[name="pwd"]').focus(function() {
		$('input[name="cad"][value="access"]').attr('checked', 'checked').change();
	});
	$('input[name="cep"]').setMask();
	
	// validate ------------------------------------------------------------------------------
	$('#main-form').validate({
		//debug: true,
		rules: {
			email: {
				required: true,
				email: <?php echo G_BUILDDEBUG ? 'false' : 'true'; ?>
			},
			cep: {
				required: "input[name='cad'][value='join']:checked", 
				cep: "input[name='cad'][value='join']:checked"
			},
			pwd: {
				required: "input[name='cad'][value='access']:checked"
			}
		},
		messages: {
			email: {
				required: "Você deve informar seu e-mail",
				email: "Este e-mail não é válido"
			},
			cep: "Informe um CEP",
			pwd: "Informe sua senha"
		},
		invalidHandler: function(form, validator) {
			submit_attempted = true;
		},
		errorPlacement: function(error, element) {
			error.insertAfter( element.parent() );
		}
	});
});
</script>


<span id="login">


<div class="content">

	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_login.png'); ?>);"></div>
	<hr class="line"></hr>
	
	<form method="post" id="main-form" class="model-widgets">
		<?php if($page->view_data->login_fail) { ?>
			<div class="message-super-bar">
				E-mail ou senha inválidos
			</div>
		<?php } ?>
		
		<div class="wraper-area">
			<div><strong class="txt-hardgray">Insira seu e-mail:</strong></div>
			<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="email" <?php if($page->view_data->email_registered) echo 'class="error" value="'.$_POST['email'].'"'; ?> /></div>
			<?php if($page->view_data->email_registered) { ?>
				<label class="error">Este e-mail já está cadastrado</label>
			<?php } ?>
		</div>
		
		<div class="wraper-area lesser">
			<label class="txt-hardgray"><input type="radio" name="cad" value="access" checked="checked" tabindex="999" />Já tenho cadastro no site.</label>
			<div>Insira sua <strong class="txt-marine">senha</strong> para poder continuar suas compras:</div>
			<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="password" name="pwd" /></div>
		</div>
		
		<div class="wraper-area lesser">
			<label class="txt-hardgray"><input type="radio" name="cad" value="join" <?php if( isset($_GET['cadastrar']) ) echo 'checked="checked"'; ?> tabindex="999" />Minha primeira compra no site: </label>
			<div>Informe seu <strong class="txt-marine">CEP</strong> para prosseguir com o cadastro:</div>
			<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="cep" alt="cep" /></div>
			<div class="min">Após a validação do CEP, você iniciará seu cadastro.</div>
		</div>
		
		
		<input type="image" src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_continuar.png'); ?>" />
		<input type="hidden" name="form_sent" />
	</form>
	
</div>


</span>