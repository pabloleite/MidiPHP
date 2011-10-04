<?php
class ModuleDataView
{
	static $view_expanded;
	static $filters;
	static $filters_data;
	
	static function PrintPersistence()
	{
		if( self::$filters )
			return serialize(self::$filters_data);
	}
	
	static function BuildView()
	{
		$md = new ModuleDataView();
		if( $md->CheckRequest() )
		{
			$_SESSION['persistence'] = unserialize(stripslashes(htmlspecialchars_decode($_POST['persistence'])));
			
			// redirect
			$url = Utils::FULL_URL();
			if( $md->redir_hash )
				$url .= '&order='.$md->order_id;
			echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
			die();
		}
		
		// view parameters
		if( $_POST['filters-submited'] )
			self::$filters_data = $_POST;
		else {
			self::$filters_data = $_SESSION['persistence'];
			unset($_SESSION['persistence']);
		}
		self::$filters = !empty( self::$filters_data );
		
		if( !empty($_GET['order']) )
			self::$view_expanded = intval($_GET['order']);
	}
	
	
	protected $redir_hash;
	protected $order_id;
	
	function CheckRequest()
	{
		global $db, $mainSession;
		
		if( !empty($_POST['request']) )
		{
			parse_str($_POST['request'], $request);
			parse_str($_POST['request_form'], $request_form);
			$this->order_id = $request['id'];
			
			switch( $request['action'] )
			{
			case 'rmv':
				$db->UpdateRows(
						$mainSession, 
						array('state' => OrderStateConsts::STATE_REMOVED), 
						array('id_pedido' => $this->order_id));
				break;
			
			case 'update':
				$order = new ActiveOrder($this->order_id);
				$this->redir_hash = true;
				
				if( $request['subaction']=='next' ) {
					$substate_data = $order->ModifySubstateData(array('substate' => 10));
					
					$values['state'] = $order->state+1;
					$values['substate'] = 0;
					$values['substate_data'] = MySQL::SQLValue($substate_data);
					$db->UpdateRows(ShopDBConsts::TABLE_ORDER, $values, array('id_pedido' => $this->order_id));
				} elseif( $request['subaction']=='done' ) {
					$db->UpdateRows(ShopDBConsts::TABLE_ORDER, array('substate' => 10), array('id_pedido' => $this->order_id));
				} elseif( $order->state==OrderStateConsts::STATE_STEP1 && $request['subaction']=='cancel' ) {
					$values['state'] = OrderStateConsts::STATE_CANCELED;
					$db->UpdateRows(ShopDBConsts::TABLE_ORDER, $values, array('id_pedido' => $this->order_id));
					
					// repoe os itens do pedido
					$sql = "SELECT * FROM ".ShopDBConsts::TABLE_ORDER_PRODUCTS." WHERE id_pedido = {$this->order_id}";
					foreach( $db->QueryArray($sql) as $rowp )
					{
						$values = array('estoque' => 'estoque + '.$rowp['quantidade']);
						$db->UpdateRows(ShopDBConsts::TABLE_PRODUCTS, $values, array('id' => $rowp['id_produto']));
					}
				} elseif( $order->state==OrderStateConsts::STATE_STEP3 && $request['subaction']=='refresh' ) {
					$substate_data['estado_envio'] = $request_form['state3_estado_envio'];
					$substate_data['rastreamento'] = $request_form['state3_rastreamento'];
					$substate_data = $order->ModifySubstateData($substate_data);
					$db->UpdateRows(ShopDBConsts::TABLE_ORDER, array('substate_data' => MySQL::SQLValue($substate_data)), array('id_pedido' => $this->order_id));
					
					if( $request_form['state3_knowcod_check'] )
					{
						$order->FetchClient();
						$mail_data['nome'] = FormatLayer::ClientFirstName($order->client['ident_nome']);
						$mail_data['orderid'] = FormatLayer::FormatOrderID($this->order_id);
						$mail_data['data'] = date('d/m/Y', strtotime($order->fetch['data_ordered']));
						$mail_data['rastreamento'] = $request_form['state3_rastreamento'];
						
						switch( $request_form['state3_estado_envio'] )
						{
						case 0 : $mail_data['state'] = 'Pronto para envio'; break;
						case 1 : $mail_data['state'] = 'Enviado'; break;
						case 2 : $mail_data['state'] = 'Entregue'; break;
						}
						
						chdir('../');
						require 'email/mail_cliente_situacao.php';
						KudosFunctions::SendEmail(
											$local_mc, 
											$mail_data['nome'].' - O seu pedido no site da Vinícola Perini foi  atualizado', 
											$order->client['login_email']);
					}
				}
				break;
				
			}
			return true;
		}
	}
}
ModuleDataView::BuildView();


// SQL parameters: filters
if( ModuleDataView::$filters )
{
	if( !isset(ModuleDataView::$filters_data['filter_opt0']) )
	{
		if( isset(ModuleDataView::$filters_data['filter_opt1']) )
			$where[] = "state = 1";
		if( isset(ModuleDataView::$filters_data['filter_opt2']) )
			$where[] = "state = 2";
		if( isset(ModuleDataView::$filters_data['filter_opt3']) )
			$where[] = "state = 3";
		if( isset(ModuleDataView::$filters_data['filter_opt4']) )
			$where[] = "state = 4";
		if( isset(ModuleDataView::$filters_data['filter_opt_data']) )
		{
			switch( ModuleDataView::$filters_data['search_mode'] )
			{
			case 1 ://hoje
				$sqlfilter = "DATE(data_ordered) = CURDATE()";
				break;
			case 2 ://esta semana
				$sqlfilter = "WEEK(data_ordered) = WEEK(CURDATE())";
				break;
			case 3 ://este mês
				$sqlfilter = "MONTH(data_ordered) = MONTH(CURDATE())";
				break;
			default ://date interval
				$invalid_filterdate = empty(ModuleDataView::$filters_data['search_date1']) || empty(ModuleDataView::$filters_data['search_date2']);
				if( !$invalid_filterdate )
				{
					$view_filterdate = true;
					$date1 = KudosFunctions::converter_data(ModuleDataView::$filters_data['search_date1']);
					$date2 = KudosFunctions::converter_data(ModuleDataView::$filters_data['search_date2']);
					$sqlfilter = "data_ordered BETWEEN '$date1' AND ADDDATE('$date2', 1)";
					break;
				}
			}
			
			if( $sqlfilter && !$invalid_filterdate )
			{
				$view_havefilter = true;
				$where[] = $sqlfilter;
			}
		}
		
		if( !empty($where) )
			$sql_where = " AND (" .implode(" OR ", $where). ")";
	}
}


// SQL parameters: sorting / page limit
global $is_ordered;
if( $is_ordered = !empty($_GET['sortc']) )
	$order_col = $_GET['sortc'];
else
	$order_col = 2;
$arr_sortfields = array('', 'id_pedido', 'id_usuario', 'data_ordered', 'data_lastevent');
$sql_order = 'ORDER BY ' .$arr_sortfields[ $order_col ].($_GET['sortd'] ? ' DESC' : ' ASC');

$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
$limit = 10;
$begin = $limit * $page;

// SQL for listing
$where = array();
$where[] = 'state > '.OrderStateConsts::STATE_INACTIVE;
$where[] = 'state <> '.OrderStateConsts::STATE_REMOVED;
$where = MySQL::BuildSQLWhereClause($where);

$sql = "SELECT SQL_CALC_FOUND_ROWS *, 
			DATE_FORMAT(data_ordered, '%%d/%%m/%%Y') as view_data, 
			DATE_FORMAT(data_lastevent, '%%d/%%m/%%Y') as view_lastevent 
		FROM $mainSession 
		%s 
		%s 
		%s";
$sql = sprintf($sql, $where.$sql_where, $sql_filter, $sql_order);
$sql .= sprintf(" LIMIT %d, %d", max(0,$begin), $limit);
$records = $db->QueryArray($sql);
//die($sql);

// pagination calcs
$actpagintes = $db->RowCount();
$totalpagitens = $db->QuerySingleValue("SELECT FOUND_ROWS()");
$totalpaginas = ceil($totalpagitens/$limit);
?>

<link rel="stylesheet" href="modules/loja_pedidos/styles.css" />


<div id="order-module">
	
	<form id="taskbar" action="?ir=loja_pedidos" method="post">
		<div id="filters">
			<div class="label"><img src="gfxnew/taskbar_header1.gif" /></div>
			
			<ul>
				<li>
					<label>
						<input type="checkbox" class="filter_opt" name="filter_opt0" value="0" id="allcheck" <?php if( isset(ModuleDataView::$filters_data['filter_opt0']) ) echo 'checked="checked"'; ?> />
						<strong>TODOS PEDIDOS</strong>
					</label>
				</li>
				
				<li>
					<label>
						<input type="checkbox" class="filter_opt" name="filter_opt1" value="1" <?php if( isset(ModuleDataView::$filters_data['filter_opt0']) || isset(ModuleDataView::$filters_data['filter_opt1']) ) echo 'checked="checked"'; ?> />
						AGUARDANDO APROVAÇÃO
						<span class="stepli"><em>ETAPA 1</em></span>
					</label>
				</li>
				
				<li>
					<label>
						<input type="checkbox" class="filter_opt" name="filter_opt2" value="2" <?php if( isset(ModuleDataView::$filters_data['filter_opt0']) || isset(ModuleDataView::$filters_data['filter_opt2']) ) echo 'checked="checked"'; ?> />
						AGUARDANDO PAGAMENTO
						<span class="stepli"><em>ETAPA 2</em></span>
					</label>
				</li>
				
				<li>
					<label>
						<input type="checkbox" class="filter_opt" name="filter_opt3" value="3" <?php if( isset(ModuleDataView::$filters_data['filter_opt0']) || isset(ModuleDataView::$filters_data['filter_opt3']) ) echo 'checked="checked"'; ?> />
						PEDIDOS PARA ENVIO
						<span class="stepli"><em>ETAPA 3</em></span>
					</label>
				</li>
				
				<li>
					<label>
						<input type="checkbox" class="filter_opt" name="filter_opt4" value="4" <?php if( isset(ModuleDataView::$filters_data['filter_opt0']) || isset(ModuleDataView::$filters_data['filter_opt4']) ) echo 'checked="checked"'; ?> />
						PEDIDOS CONCLUÍDOS
					</label>
				</li>
				
				<li class="nobg">
					<label>
						<input name="filter_opt_data" type="checkbox" <?php if($view_havefilter) echo 'checked="checked"'; ?> />
						COMPRADOS NO PERÍODO
					</label>
					
					<div id="search_tblock">
						<div id="searchbox1">
							<label><input name="search_mode" type="radio" value="1" <?php if( $view_havefilter && $_REQUEST['search_mode']==1 ) echo 'checked="checked"'; ?> />Hoje</label><br />
							<label><input name="search_mode" type="radio" value="2" <?php if( $view_havefilter && $_REQUEST['search_mode']==2 ) echo 'checked="checked"'; ?> />Esta semana</label><br />
							<label><input name="search_mode" type="radio" value="3" <?php if( $view_havefilter && $_REQUEST['search_mode']==3 ) echo 'checked="checked"'; ?> />Este mês</label><br />
						</div>
						<!--<div class="searchbtn" style="margin-top: 35px;"><img src="gfxnew/btn_bg.gif" /> ENVIAR</div>-->
						
						<div id="searchbox2">
							<div class="search"><div>Data inicial:</div><input name="search_date1" type="text" class="search_input" value="<?php if($view_filterdate) echo $_REQUEST['search_date1']; ?>" /></div>
							<div class="search"><div>Data final:</div><input name="search_date2" type="text" class="search_input" value="<?php if($view_filterdate) echo $_REQUEST['search_date2']; ?>" /></div>
						</div>
						
						<script type="text/javascript">
						$(function() {
							//$.datepicker.setDefaults($.extend({showMonthAfterYear: false},$.datepicker.regional['']));
							//$('.search_input').datepicker($.datepicker.regional['pt-BR']);
							//$('.search_input').datepicker('option', {showAnim: 'slide'});
						});
						</script>
						
						<!--<div class="searchbtn" style="margin-top: 61px;"><img src="gfxnew/btn_bg.gif" /> ENVIAR</div>-->
					</div>
					
					<script type="text/javascript">
					$('.search_input').focus(function() {
						$('[name="search_mode"]').removeAttr("checked");
					});
					</script>
				</li>
			</ul>
			
			<div id="search-refbtn">
				<input type="hidden" name="filters-submited" value="1" />
				<input type="image" src="gfxnew/btn_refresh.gif" />
			</div>
			
			<script type="text/javascript">
				$('.filter_opt').click(function() {
					var checkval = $(this).val();
					var checked_status = this.checked;
					if(checkval==0)
					{
						$('.filter_opt').each(function() {
							this.checked = checked_status;
						});
					} else {
						if(!checked_status)
							$('#allcheck').attr('checked', false);
					}
				});
				
				<?php if(!$view_havefilter) { ?>
					$('#search_tblock').hide();
				<?php } ?>
				
				$('[name="filter_opt_data"]').click(function() {
					if( $('#search_tblock').is(':visible') )
						$('#search_tblock').hide();
					else
						$('#search_tblock').show();
				});
			</script>
			
		</div>
		
		<div id="relat">
			<div class="label"><img src="gfxnew/taskbar_header3.gif" /></div>
			<div id="selfheader">
				<div class="lbl" style="width: 121px;">Situação</div>
				<div class="lbl" style="width: 84px;">Qtd.</div>
				<div class="lbl" style="width: 186px;">Gráfico Demonstrativo</div>
				<div class="lbl">Porcentagem</div>
			</div>
			
			<?php
			$db->Query("SELECT * FROM ".ShopDBConsts::TABLE_ORDER." WHERE state > 0");
			$cnt_total = (int) $db->RowCount();
			$db->Query("SELECT * FROM ".ShopDBConsts::TABLE_ORDER." WHERE state = 1");
			$cnt_state1 = (int) $db->RowCount();
			$db->Query("SELECT * FROM ".ShopDBConsts::TABLE_ORDER." WHERE state = 2");
			$cnt_state2 = (int) $db->RowCount();
			$db->Query("SELECT * FROM ".ShopDBConsts::TABLE_ORDER." WHERE state = 3");
			$cnt_state3 = (int) $db->RowCount();
			$db->Query("SELECT * FROM ".ShopDBConsts::TABLE_ORDER." WHERE state = 4");
			$cnt_state4 = (int) $db->RowCount();
			$db->Query("SELECT * FROM ".ShopDBConsts::TABLE_ORDER." WHERE state > 4");
			$cnt_finished = (int) $db->RowCount();
			$cnt_tosend = $cnt_state1 + $cnt_state2;
			
			// get marginal values
			$widths_arr = array($cnt_tosend, $cnt_state1, $cnt_state2, $cnt_state3, $cnt_state4, $cnt_finished);
			$sorted_arr = $widths_arr;
			asort($sorted_arr);
			$sub_slices = reset($sorted_arr);
			$diff = end($sorted_arr) - $sub_slices;
			assert($diff>=0);
			if( $sub_slices>0 )
			{
				$sub_slices--;
				$diff++;
			}
			
			// setup widths
			$maxwidth = 150;
			//$nslices = max() TO-DO
			$nslices = max(1, $diff);
			$bar_slice = $maxwidth / $nslices;
			foreach($widths_arr as &$width)
				$width = $width ? $bar_slice*($width-$sub_slices) : $width;
			
			$relat_types = array(
				'PENDENTES (1 ou 2)', 
				'EM APROVAÇÃO (1)', 
				'AGUARDANDO PAGAMENTO (2)', 
				'PARA ENVIO (3)',
				'CONCLUÍDOS', 
				'CANCELADOS OU EXCLUÍDOS'
			);
			$relat_values = array($cnt_tosend, $cnt_state1, $cnt_state2, $cnt_state3, $cnt_state4, $cnt_finished);
			$relat_progs = array();
			for($idx=0; $idx < count($relat_types); $idx++)
				$relat_progs[$idx] = number_format( 100*($relat_values[$idx] / $cnt_total), 2, ',', '' );
			?>
			
			<table border="0" cellspacing="0" cellpadding="0">
				<?php
				for($idx=0; $idx < count($relat_types); $idx++)
				{
				?>
				<tr class="<?php if(++$tri%2 == 1) echo 'tr1';?>">
					<td style="width: 100px; padding-left: 17px;"><?php echo $relat_types[$idx]; ?></td>
					<td style="width: 70px; padding-right: 37px;" align="center"><?php echo $relat_values[$idx]; ?></td>
					<td style="width: 183px;"><img src="gfxnew/bar_later.gif" /><?php for($i=0; $i<$widths_arr[$idx]; $i++) { ?><img src="gfxnew/bar_body.gif" /><?php } ?><img src="gfxnew/bar_later.gif" /></td>
					<td style="width: 120px;"><?php echo $relat_progs[$idx]; ?>%</td>
				</tr>
				<?php
				}
				?>
			</table>
		</div>
	</form>
	
	<form action="?ir=loja_pedidos" method="post" id="list-form">
		<input type="hidden" name="persistence" value="<?php echo htmlspecialchars(ModuleDataView::PrintPersistence()); ?>" />
		<input type="hidden" name="request" />
		<input type="hidden" name="request_form" />
	
		<div id="listbar"></div>
		
		<div id="listtbl">
			<table border="0" cellspacing="0" cellpadding="3">
			<?php if($actpagintes==0) { ?>
				<tr class="trorder" valign="middle">
					<td class="bold">NENHUM REGISTRO ENCONTRADO</td>
				</tr>
			<?php } else { ?>
				<?php
				function OutputOrderImg($label)
				{
					global $is_ordered;
					static $isort = 0; $isort++;
					$anchor = sprintf('<a href="?ir=loja_pedidos&sortc=%d&sortd=%d" class="header_sort">%%s</a>', $isort, $_GET['sortd'] ? 0 : 1);
					if( $is_ordered && $_GET['sortc']==$isort )
						printf($anchor, $label.'<img src="gfxnew/arrow_sort_' .($_GET['sortd'] ? 'down' : 'up'). '.gif" class="high" />');
					else
						printf($anchor, '<img src="gfxnew/arrow_sort_hint.gif" />'.$label);
				}
				?>
				<tr id="supheader">
					<th width="80"><?php echo OutputOrderImg('CÓDIGO'); ?></th>
					<th width="220"><?php echo OutputOrderImg('CLIENTE'); ?></th>
					<th width="150"><?php echo OutputOrderImg('DATA DO PEDIDO'); ?></th>
					<th width="175"><?php echo OutputOrderImg('ÚLTIMA ALTERAÇÃO'); ?></th>
					<th>ETAPAS DO PROCESSO</th>
					<th width="90">AÇÕES</th>
				</tr>
				
				<tr id="supsepar">
					<th><img src="gfxnew/listtbl_headind.gif" /></th>
					<th><img src="gfxnew/listtbl_headind.gif" /></th>
					<th><img src="gfxnew/listtbl_headind.gif" /></th>
					<th><img src="gfxnew/listtbl_headind.gif" /></th>
					<th><img src="gfxnew/listtbl_headind.gif" /></th>
					<th><img src="gfxnew/listtbl_headind.gif" /></th>
				</tr>
				
				<?php
				foreach( $records as $rowp ) {
					$order = new ActiveOrder($rowp);
					$order->FetchClient();
				?>
				
					<tr class="trorder state<?php echo $order->state; ?>" valign="middle">
						<td class="bold"><?php echo FormatLayer::FormatOrderID($rowp['id_pedido']); ?></td>
						<td class="bold"><?php echo strtoupper($order->client['ident_nome']); ?></td>
						<td><?php echo $rowp['view_data']; ?></td>
						<td><?php echo empty($rowp['view_lastevent']) ? $rowp['view_data'] : $rowp['view_lastevent']; ?></td>
						<td>
							<div id="progchart" style="background-image: url(gfxnew/listtbl_prog<?php echo $order->state; ?>.gif); width: 112px;">
								<!--<div id="status"><?php echo $view_msg; ?></div>-->
							</div>
						</td>
						<td>
							<a href="<?php echo $rowp['id_pedido'];?>" class="btnexpand" <?php if(ModuleDataView::$view_expanded==$rowp['id_pedido']) echo 'id="forceopen"';?>><div id="togglebtn"></div></a>&nbsp;&nbsp;
							<a href="<?php echo $rowp['id_pedido'];?>" class="btnrmv"><img src="gfxnew/listtbl_act_del.gif" /></a>
						</td>
					</tr>
					
					<tr class="trpreload">
						<td colspan="6" align="center"><img src="gfxnew/preloader.gif" /></td>
					</tr>
				
				<?php } ?>
			<?php } ?>
			</table>
		</div>
	</form>
	
</div>


<script type="text/javascript">
$(function() {
	<?php if( $is_ordered ) { ?>
	setTimeout(function() {
		$('html,body').animate({scrollTop: $('#listtbl').offset().top}, 400);
	}, 250);
	//$('html,body').animate({scrollTop: $('#listtbl').offset().top}, 'slow');
	<?php } ?>
	
	// register list actions click
	$('.btnrmv').click(function() { ItemRemove($(this).attr('href')); return false; });
	$('.btnexpand').click(function() { ItemToggleOpen($(this), $(this).attr('href')); return false; });
	
	// open an expanded item
	var forceopen = $('#forceopen');
	if(forceopen[0])
	{
		forceopen.click();
		
		setTimeout(function() {
			$('html,body').animate({scrollTop: forceopen.offset().top}, 400);//or scrollIntoView
		}, 250);
	}
});


// ---------------------------------------------------------------
function ItemRemove(id)
{
	if( confirm('Deseja realmente excluir este pedido?') )
		RequestSubmit(id, {action: 'rmv'});
}

function ItemToggleOpen(_this, id)
{
	var parent_tr = $(_this).closest('.trorder');
	if( parent_tr.hasClass('open') )
	{
		parent_tr.next().remove();
		parent_tr.removeClass('open');
		parent_tr.removeClass('loaded');
	} else {
		parent_tr.addClass('open');
		parent_tr.next().show();
		$.get(
			'modules/loja_pedidos/pedidos_expand.php',
			{ id: id }, 
			function(data) {
				parent_tr.next().hide();
				parent_tr.after(data);
			}
		);
	}
}

// ---------------------------------------------------------------


function RequestSubmit(id, data, jq_params)
{
	var sdata = $.extend( {id: id}, data );
	$('input[name="request"]').val( $.param(sdata) );
	if(jq_params)
		$('input[name="request_form"]').val( jq_params.serialize() );
	$('#list-form').submit();
}

</script>
