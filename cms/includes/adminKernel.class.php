<?
/*!
 * AdminKernel.class v0.0.1
 *
 * Data: 15-09-2009 17:34:21 (quinta-feira, 15 Setembro  2009)
 * Revisado: 0002
 
 * Arquivo modificado dia 15/09/09 10:30 nas linhas : 
 * 131 - Função function getSendMailHref() - Para abrir o arquivo form.php "Contato" utilizando a função ondblclick="window.location"
 * 134 - Função function getSendMail() - Função responsavel pelo icone email.gif que aparece na lista de contatos no arquivo main.php "Contato"
 * 173 - função modificada function translateMsgInfo() - Foram acrescentados novos valores correspondentes a mensagens de aviso quando retornado ao arquivo main.php "Contato"nas linhas
      * 178 -  "6"=>"Sua mensagem foi enviada com sucesso!",
      * 179	-  "7"=>"Não foi possivel enviar a mensagem. Verifique todos os campos.");
*/
require_once("utils.class.php");

class AdminKernel {
	public function showAdminMenu($modulos){
		foreach($modulos as $modulo) {
			if ($modulo['visivel']=='n') continue;
			
			if (count($modulo['submenus'])>0) {
				echo "<div class=\"itemMenu\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				  <tr>
					<td height=\"38\" width=\"15\" valign=\"middle\"><img src=\"gfx/tickItemMenu.gif\" width=\"4\" height=\"9\" /></td>
					<td valign=\"middle\"><a href=\"javascript:void(0);\" onclick=\"$('#submenu_".$modulo["id_modulo"]."').toggle();\">".$modulo["label"]."</a></td>
				  </tr>
				</table>
				</div>\n";
				
				echo "<div id=\"submenu_".$modulo["id_modulo"]."\" class=\"submenu\" style=\"display:none;\">";
				$openSubmenuScript="";
				foreach($modulo['submenus'] as $submenu) {
					$checkedLink = $_REQUEST['ir']==$submenu["nome"]?"class=\"checkedMenu\"":"";
					
					if ($openSubmenuScript=='' && $checkedLink != "") {
						$openSubmenuScript="<script>$('#submenu_".$modulo["id_modulo"]."').toggle();</script>";
					}
					echo "<div class=\"itemSubmenu\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
							  <tr>
								<td height=\"28\" width=\"15\" valign=\"middle\"><img src=\"gfx/tickItemMenu.gif\" width=\"4\" height=\"9\" /></td>
								<td valign=\"middle\"><a href=\"index.php?ir=".$submenu["nome"]."\" $checkedLink>".$submenu["label"]."</a></td>
							  </tr>
							</table>
							</div>\n";
				}
				echo "</div>";
				echo $openSubmenuScript;
			} else {
				$checkedLink = $_REQUEST['ir']==$modulo["nome"]?"class=\"checkedMenu\"":"";
				echo "<div class=\"itemMenu\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				  <tr>
					<td height=\"38\" width=\"15\" valign=\"middle\"><img src=\"gfx/tickItemMenu.gif\" width=\"4\" height=\"9\" /></td>
					<td valign=\"middle\"><a href=\"index.php?ir=".$modulo["nome"]."\" $checkedLink>".$modulo["label"]."</a></td>
				  </tr>
				</table>
				</div>\n";
			}
		}
	}
	public function showAuxMenu() {
		global $_SESSION;
		echo "<div class=\"itemMenu\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				  <tr>
					<td height=\"38\" width=\"15\" valign=\"middle\"><img src=\"gfx/tickItemMenu.gif\" width=\"4\" height=\"9\" /></td>
					<td valign=\"middle\"><a href=\"index.php\">Ir para a Página Inicial</a></td>
				  </tr>
				</table>
				</div>\n";	
		if ($_SESSION["usuario_dados"]["tipo"]=='M') {
			echo "<div class=\"itemMenu\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
					  <tr>
						<td height=\"38\" width=\"15\" valign=\"middle\"><img src=\"gfx/tickItemMenu.gif\" width=\"4\" height=\"9\" /></td>
						<td valign=\"middle\"><a href=\"index.php?ir=usuarios\">Usuários</a></td>
					  </tr>
					</table>
					</div>\n";	
		}	
		echo "<div class=\"itemMenu\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				  <tr>
					<td height=\"38\" width=\"15\" valign=\"middle\"><img src=\"gfx/tickItemMenu.gif\" width=\"4\" height=\"9\" /></td>
					<td valign=\"middle\"><a href=\"index.php?ir=configuracoes\">Configurações</a></td>
				  </tr>
				</table>
				</div>\n";		
		echo "<div class=\"itemMenu\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				  <tr>
					<td height=\"38\" width=\"15\" valign=\"middle\"><img src=\"gfx/tickItemMenu.gif\" width=\"4\" height=\"9\" /></td>
					<td valign=\"middle\"><a href=\"index.php?logout=1\">Sair do painel</a></td>
				  </tr>
				</table>
				</div>\n";	
	}
	public function checkChangeResultsPerPage() {
		global $_REQUEST;
		global $_SESSION;
		if ($_REQUEST['resultsPerPage'] && (int)$_REQUEST['resultsPerPage'] > 0) {
			$_SESSION['resultsPerPage'] = $_REQUEST['resultsPerPage'];
		}
	}
	public function getListDataImage($image, $thumbWidth=200, $thumbHeight=200) {
		if(is_file($image)) {
			$originalDimensions = getimagesize($image);
			if( strpos($image,'../')===0 )
				$image = substr($image, 3);
			return "<br /><a href=\"javascript:void(0);\" onClick=\"popUp('zoom.php?imgSrc=".$image."', ". ($originalDimensions[0]+10) .",". ($originalDimensions[1]+10) .")\"><img src=\"../thumb/?src=".$image."&w=".$thumbWidth."&h=".$thumbHeight."\"></a><br clear=\"all\" />";
		} else {
			return false;
		}
	}
	public function getIncLink() {
		return "<p><a href=\"?".Utils::queryString('msgInfoId')."&action=inc\"><img src=\"gfx/btAdicionar.gif\" /></a></p>";
	}
	public function getAltHref($id,$requestId='id') {
		return "?".Utils::queryString('msgInfoId')."&action=alt&".$requestId."=".$id;
	}
	public function getAltLink($id,$requestId='id') {
		return "<a title=\"Editar\" href=\"?".Utils::queryString('msgInfoId')."&action=alt&".$requestId."=".$id."\"><img src=\"gfx/ico/alt.gif\" width=\"25\" height=\"25\" /></a>";
	}
	public function getExcLink($id,$requestId='id') {
		return "<a href=\"?".Utils::queryString('msgInfoId')."&action=exc&".$requestId."=".$id."\" onclick=\"return confirm('Deseja realmente excluir este registro?');\"><img src=\"gfx/ico/exc.gif\" width=\"25\" height=\"25\" /></a>";
	}
	public function getSendMailHref($id,$requestId='id'){ // Função para enviar email utilizando a função ondblclick="window.location" 
		return "?".Utils::queryString('msgInfoId')."&action=send&".$requestId."=".$id;
	}
	public function getSendMail($id,$requestId='id'){ // Função responsavel pelo icone email.gif que aparece na lista de contatos.
		return "<a href=\"?".Utils::queryString('msgInfoId')."&action=send&".$requestId."=".$id."\"><img src=\"gfx/ico/enviarNews.gif\" width=\"25\" height=\"25\" /></a>";
	}
	public function getMultipleExclusionLink() {
		return "<div id=\"excludeMultiplesBt\"><a href=\"javascript:void(0);\" onclick=\"excludeMultiple(document.getElementById('multipleExclusionForm'));\"><img src=\"gfx/ico/exc.gif\" align=\"absmiddle\" /> Excluir selecionados</a></div>";
	}
	public function getThOrderLabel($label, $orderCollumn) {
		global $_REQUEST;
		$orderDir = "ASC";
		$orderImage = "";
		
		if ($orderCollumn == $_REQUEST['order']) {
			if (strtoupper($_REQUEST['dir'])=="ASC") {
				$orderDir = "DESC";
				$orderImage = "<img src=\"gfx/orderAsc.gif\" />";
			} else {
				$orderDir = "ASC";
				$orderImage = "<img src=\"gfx/orderDesc.gif\" />";
			}
		}
		
		return "<a href=\"?".Utils::queryString("action,id,order,dir")."&order=$orderCollumn&dir=$orderDir\">".$label." ".$orderImage."</a>";
	}
	public function showModuleTitle($sessionName, $remove="action,id") {
		global $_REQUEST;
		echo "<h1>";
		echo "<a href=\"?".Utils::queryString($remove)."\">".$sessionName."</a>";
		if ($_REQUEST['action']=='inc') {
			echo " :: Inclusão";
		}
		if ($_REQUEST['action']=='alt') {
			echo " :: Alteração";
		}
		echo "</h1>";
	}
	public function translateMsgInfo() {
		global $_REQUEST;
		global $_SESSION;
		$msgInfo = array("0"=>"Erro ao processar solicitação.", 
						 "1"=>"Registro inserido com sucesso.", 
						 "2"=>"Registro alterado com sucesso.", 
						 "3"=>"Registro excluido com sucesso.", 
						 "4"=>"Registros excluidos com sucesso.", 
						 "5"=>"Suas configurações foram salvas.",
						 "6"=>"Sua mensagem foi enviada com sucesso!",
						 "7"=>"Não foi possivel enviar a mensagem. Verifique todos os campos.");
		
		if (isset($_REQUEST["msgInfoId"])) {
			if (in_array($_REQUEST["msgInfoId"],array_flip($msgInfo))) {
				return "<div id=\"msgInfo\"><hr /><p style=\"text-align:center;\"><img src=\"gfx/ico/info.gif\" align=\"absmiddle\" /> <strong>".$msgInfo[$_REQUEST["msgInfoId"]]."</strong></p><hr /></div>";
			}
		} elseif (isset($_REQUEST["msgInfo"])) {
			return "<div id=\"msgInfo\"><hr /><p style=\"text-align:center;\"><img src=\"gfx/ico/info.gif\" align=\"absmiddle\" /> <strong>".$_REQUEST["msgInfo"]."</strong></p><hr /></div>";
		}
		
	}
	
	public function showBtVoltarHome() {
		echo "<br clear=\"all\" /><br clear=\"all\" /><p><a href=\"index.php\"><img src=\"gfx/btVoltarHome.gif\" width=\"132\" height=\"13\" /></a></p>";
	}
	public function showBtVoltar() {
		echo "<br clear=\"all\" /><p><a href=\"javascript:history.go(-1);\"><img src=\"gfx/btVoltar.gif\" width=\"45\" height=\"13\" /></a></p>";
	}
	public function showMsgInfo($msg="") {
		echo "<p><img src=\"gfx/ico/info.gif\" align=\"absmiddle\" /> <strong class=\"maior\">".$msg."</strong></p>";
	}
}
?>