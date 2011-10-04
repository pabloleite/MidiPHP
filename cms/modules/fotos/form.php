<?php
global $db;

$alt = $_REQUEST['action']=='alt';
if( $alt && $_REQUEST[id] ) {
	$db->Query("SELECT $mainSession.*
				FROM $mainSession 
				WHERE $primaryKey = '$_REQUEST[$primaryKey]'");
	
	if( $db->RowCount()>0 )
		$row = $db->RowArray();
}
?>

<?php
if ($_REQUEST[action]=='inc') {
?>
	<script language="javascript">
		function appletInitialized( applet ) {
			traceEvent( "appletInitialized, " + applet.getAppletInfo() );
		}
		function uploaderFilesReset( uploader ) {
			traceEvent( "uploaderFilesReset, fileCount=" + uploader.getFileCount() );
		}
		function uploaderFileAdded( uploader, file ) {
			traceEvent( "uploaderFileAdded, index=" + file.getIndex() );
		}
		function uploaderFileRemoved( uploader, file ) {
			traceEvent( "uploaderFileRemoved, path=" + file.getPath() );
		}
		function uploaderFileMoved( uploader, file, oldIndex ) {
			traceEvent( "uploaderFileMoved, path=" + file.getPath() + ", oldIndex=" + oldIndex );
		}
		function uploaderFileStatusChanged( uploader, file ) {
			dumpUploaderStatus();
			traceEvent( "uploaderFileStatusChanged, index=" + file.getIndex() + ", status=" + file.getStatus() + ", content=" + file.getResponseContent() );
		}
		function uploaderStatusChanged( uploader ) {
			traceEvent( "uploaderStatusChanged, status=" + uploader.getStatus() );
		}
		function uploaderSelectionChanged( uploader ) {
			traceEvent( "uploaderSelectionChanged" );
		}
	
	
		function traceEvent( message ) {
			//document.getElementById( "uploaderStatus" ).innerHTML += message + "<br />";
		}
		
		function dumpUploaderStatus() {
			var uploader = document.jumpLoaderApplet.getUploader();
			
			var uploaderDump = "<br /><strong>Status do Upload:</strong><br>" +
				"Status: " + uploader.getStatus() + "<br>" +
				"Total de arquivos: " + uploader.getFileCount() + "<br>" +
				"Arquivos restantes: " + uploader.getFileCountByStatus( 0 ) + "<br>" +
				"Em progresso: " + uploader.getFileCountByStatus( 1 ) + "<br>" +
				"Uploads finalizados: " + uploader.getFileCountByStatus( 2 ) + "<br>" +
				"Falhas: " + uploader.getFileCountByStatus( 3 ) + "<br>" +
				"Tamanho total dos arquivos: " + Math.floor(uploader.getFilesLength()/1024) + " KB";
			
			document.getElementById( "uploaderStatus" ).innerHTML = uploaderDump;
		 }
	</script>
	
	<applet name="jumpLoaderApplet" code="jmaster.jumploader.app.JumpLoaderApplet.class" archive="includes/jar/upload.jar" mayscript="" width="100%" height="500">
		<param name="uc_imageEditorEnabled" value="true">
		<param name="uc_uploadUrl" value="modules/fotos/upload.php?rel=<?=$_REQUEST[rel]?>&id_rel=<?=$_REQUEST[id_rel]?>">
		
		<param name="ac_fireAppletInitialized" value="true">
		<param name="ac_fireUploaderFileAdded" value="true">
		<param name="ac_fireUploaderFileRemoved" value="true">
		<param name="ac_fireUploaderFileMoved" value="true">
		<param name="ac_fireUploaderFileStatusChanged" value="true">
		<param name="ac_fireUploaderFilesReset" value="true">
		<param name="ac_fireUploaderStatusChanged" value="true">
		<param name="ac_fireUploaderSelectionChanged" value="true">
	</applet>
	
	<div id="uploaderStatus"></div>
<?
} elseif($_REQUEST['action']=='alt' && $_REQUEST[id]) {
?>
    <form name="oper" id="oper" enctype="multipart/form-data" method="post">
	    <div class="field"><label>Legenda</label><br /><input name="legenda" id="legenda" value="<?=$row[legenda]?>" type="text" class="inputMedium" /></div>
		
		<div class="field"><label>Imagem</label>
			<?=AdminKernel::getListDataImage($config["userfiles_path"]."/".$row['rel']."/".$row["arquivo"])?><br />
			<input type="file" name="imagem1" id="imagem" class="radio" title="Selecione um imagem" value="" />
			<?php if( $alt ) { ?>
				<br /><label class="normal"><input type="radio" name="manterArquivo1" id="manterArquivo1" value="true" checked="checked" title="Clique aqui para manter o arquivo"/>Manter arquivo</label><br />
				<label class="normal"><input type="radio" name="manterArquivo1" id="manterArquivo1" value="false" title="Clique aqui para apagar o arquivo"/>Apagar arquivo</label>
			<?php } ?>
		</div>
		
        <input type="hidden" name="enviado" value="1" />
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
			<tr>
				<td></td>
				<td width="61" valign="bottom"><a href="#" onclick="history.go(-1);"><img src="gfx/btCancelar.gif" width="61" height="18" border="0"/></a></td>
				<td width="83" valign="bottom"><input type="image" src="gfx/btEnviar.gif" class="inputImage" width="83" height="25" /></td>
			</tr>
		</table>
	</form>
<?
}
?>

<? AdminKernel::showBtVoltar(); ?>
