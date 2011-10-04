<?php
class AS_Page extends Page
{
	function PagePreInclude()
	{
		if( !Login::$logged )
			$this->MarkRedirect()->ThrowReturn();
		
		if( isset($_POST['form_sent']) )
		{
			$values['login_senha'] = sha1($_POST['senha']);
			db::update( ShopDBConsts::TABLE_USER, $values, array('id_usuario' => Login::$login_id) );
			
			$this->view_chain->success = true;
			$this->MarkRedirectChain();
		}
	}
}

$page = Site::LinkIncludePage( new AS_Page() );
$page->view_site->entire_flow = true;
?>

<?php if(!$page->view_chain->success) { ?>
<script type="text/javascript">
$(function() {
	
	// validate ------------------------------------------------------------------------------
	$('#main-form').validate({
		rules: {
			senha: {
				required:  true,
				minlength: <?php echo G_BUILDDEBUG ? '0' : '6'; ?>
			},
			repeat: {
				required:  true,
				equalTo: 'input[name="senha"]'
			}
		},
		messages: {
			senha: "Informe uma senha com no mínimo 6 caracteres",
			repeat: "Repita a senha"
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
	
	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_alterar_senha.png'); ?>);"></div>
	<hr class="line"></hr>
	
	<?php if($page->view_chain->success) { ?>
		Senha alterada com sucesso.
	<?php } else { ?>
		<form method="post" id="main-form" class="model-widgets">
			<div class="wraper-area">
				<div><strong class="txt-hardgray">Insira sua nova senha:</strong></div>
				<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="password" name="senha" /></div>
			</div>
			
			<div class="wraper-area">
				<div><strong class="txt-hardgray">Confirme a senha:</strong></div>
				<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="password" name="repeat" /></div>
			</div>
			
			
			<input type="image" src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_alterar.png'); ?>" />
			<input type="hidden" name="form_sent" />
		</form>
	<?php }?>
	
</div>


</span>