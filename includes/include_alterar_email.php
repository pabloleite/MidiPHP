<?php
class AE_Page extends Page
{
	function PagePreInclude()
	{
		if( !Login::$logged )
			$this->MarkRedirect()->ThrowReturn();
		
		if( isset($_POST['form_sent']) )
		{
			$email = trim($_POST['email']);
			db::select( ShopDBConsts::TABLE_USER, '*', array('login_email' => db::escape($email)) );
			
			if( db::affected() > 0 )
				$this->view_data->email_registered = true;
			else {
				$values['login_email'] = $email;
				db::update( ShopDBConsts::TABLE_USER, $values, array('id_usuario' => Login::$login_id) );
				Login::$glogin->RenewLoginFetch();
				
				$this->view_chain->success = true;
				$this->MarkRedirectChain();
			}
		}
	}
}

$page = Site::LinkIncludePage( new AE_Page() );
$page->view_site->entire_flow = true;
?>

<?php if(!$page->view_chain->success) { ?>
<script type="text/javascript">
$(function() {
	
	// validate ------------------------------------------------------------------------------
	$('#main-form').validate({
		rules: {
			email: {
				required: true,
				email: <?php echo G_BUILDDEBUG ? 'false' : 'true'; ?>
			},
		},
		messages: {
			email: {
				required: "Informe o seu novo e-mail",
				email: "Este e-mail não é válido"
			}
		},
		errorPlacement: function(error, element) {
			error.insertAfter( element.parent() );
		}
	});
	
});
</script>
<?php } ?>


<span id="alterar">


<div class="content">
	
	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_alterar_email.png'); ?>);"></div>
	<hr class="line"></hr>
	
	<?php if($page->view_chain->success) { ?>
		E-mail alterado com sucesso.
	<?php } else { ?>
		<form method="post" id="main-form" class="model-widgets">
			<div class="wraper-area">
				<div><strong class="txt-hardgray">Insira seu novo e-mail:</strong></div>
				<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="email" value="<?php echo $_POST['email']; ?>" /></div>
				<?php if($page->view_data->email_registered) { ?>
					<label class="error">Este e-mail já está cadastrado</label>
				<?php } ?>
			</div>
			
			
			<input type="image" src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_alterar.png'); ?>" />
			<input type="hidden" name="form_sent" />
		</form>
	<?php }?>
	
</div>


</span>