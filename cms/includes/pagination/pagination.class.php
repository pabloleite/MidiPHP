<?
require("paginationBase.class.php");

class Pagination {
	private $pageVar = 'pag';
	private $rowsPerPage = 10;
	private $maxPages = 10;
	public $totalRows = 1;
	public $currentPage = 1;
	public $lastPage = 1;
	public $startRow = 10;
	
	function __construct($rowsPerPage = 10, $maxPages = 10, $totalRows = 1) {
		global $_REQUEST;
		
		$this->rowsPerPage = $rowsPerPage;
		$this->maxPages = $maxPages;
		$this->totalRows = $totalRows;
		$this->lastPage = ceil((int)$this->totalRows / $this->rowsPerPage);
		
		$this->currentPage = empty($_REQUEST[$this->pageVar]) ? 1 : (int)$_REQUEST[$this->pageVar];
		
		if ($this->currentPage > $this->lastPage) {
			$this->currentPage = $this->lastPage;
		} elseif ($this->currentPage < 1) {
			$this->currentPage = 1;
		}
		
		if ($_REQUEST['resultsPerPage'] != "") {
			$rowsPerPage = (int)$_REQUEST['resultsPerPage'] == 0 ? 10 : $_REQUEST['resultsPerPage'];
			if ($_REQUEST['resultsPerPage']==$rowsPerPage) {
				$_SESSION['user_profile']['resultsPerPage'] = $rowsPerPage;
				//Utils::redirect("?".Utils::queryString("$this->pageVar,resultsPerPage"));
			}
		}
		
		if ($_REQUEST[$this->pageVar] != $this->currentPage) {
			//Utils::redirect("?".Utils::queryString($this->pageVar)."&".$this->pageVar."=".$this->currentPage);
		}
		
		if ($this->lastPage > 0) {
			$this->startRow = (($this->currentPage-1) * $this->rowsPerPage);
		} else {
			$this->startRow = 0;
		}
	}
	public function getPaginationSelectResults() {
		$select = "<select name=\"resultsPerPage\" id=\"resultsPerPage\" onchange=\"gotoUrl('?".Utils::queryString("resultsPerPage")."&resultsPerPage='+this.value);\">";
		for ($i = 5; $i < 40; $i+=5) {
			$selected = $i == $_SESSION['user_profile']['resultsPerPage'] ? "selected=\"selected\"":"";
			$select .= "<option value=\"$i\" $selected>$i</option>";
		}
		$select .= "</select>";
		return $select;
	}
	public function getSqlOrder($table, $primaryKey, $sqlDir = "ASC") {
		global $db;
		global $_REQUEST;
		$sqlOrder = $primaryKey;
		
		if ($db->GetColumnID(end(explode('.',$_REQUEST['order'])), $table)) {
			$sqlOrder = $_REQUEST['order'];
		}
		if ($_REQUEST['dir']=='ASC' || $_REQUEST['dir']=='DESC') {
			$sqlDir = $_REQUEST['dir'];
		}
		return "ORDER BY $sqlOrder $sqlDir";
	}
	public function getPaginationSqlLimit() {
		return "LIMIT $this->startRow, $this->rowsPerPage";
	}
	public function getPagination() {
		$paginationBase = new PaginationBase("?".Utils::queryString($this->pageVar),$this->rowsPerPage,$this->pageVar);
		return $paginationBase->MontarPaginacao($this->currentPage, $this->lastPage);
	}
	
}
?>