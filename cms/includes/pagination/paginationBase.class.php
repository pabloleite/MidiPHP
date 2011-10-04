<?php
class PaginationBase {
    private $PaginaAtual = 1; 
    private $UltimaPagina = 1;
    private $NumPgLaterais = 3;
    private $HTMLPaginacao = '';
    
    private $SiteLink = '';
    private $PageGET = 'pag';

    private $ClassePaginaAtual = 'paginacao_atual';
    private $ClasseNavegadores = 'paginacao_navegar';
	private $ClasseNavegadoresTxt = 'paginacao_navegar_txt';
    private $ClasseSeparadores = 'paginacao_separa';
    private $ClasseNavdisabled = 'barNav_disabled';

    private $TextoSeparador = '...';
    private $TextoAnterior = '< Anterior';
    private $TextoProxima = 'Pr&oacute;xima >';
    

    function __construct ($pag_link='',$NumPgLaterais=10,$varGET='pag'){
      $this->SiteLink = $pag_link; 
      $this->NumPgLaterais = $NumPgLaterais;
      $this->PageGET = $varGET;
    }
    
    function set_nomes_nave($Separ='...',$pagAnt='Anterior',$pagPro='Pr&oacute;xima'){
      $this->TextoSeparador = $Separ;
      $this->TextoAnterior = $pagAnt;
      $this->TextoProxima = $pagPro;
    }
    
    function set_layout($PgAt='paginacao_atual',$PgNav='paginacao_navegar',$PgSep='paginacao_separa',$disabled='barNav_disabled', $PgNavTxt='paginacao_navegar_txt'){
      $this->ClassePaginaAtual = $PgAt;
      $this->ClasseNavegadores = $PgNav;
	  $this->ClasseNavegadoresTxt = $PgNavTxt;
      $this->ClasseSeparadores = $PgSep;
      $this->ClasseNavdisabled = $disabled;
    }
    
    function MontarPaginacao($pgatual = 0, $pgfim = 0) {
		if ($pgfim <= 1) {
			return false;
		}
		
		
		// Adiciona o CSS para a paginação
		echo '<link rel="stylesheet" media="all" href="includes/pagination/pagination.css" />';
		
        if ($pgatual)
            $this->PaginaAtual = $pgatual;
        if ($pgfim)
            $this->UltimaPagina = $pgfim;

        if (strpos($this->SiteLink, '?') === FALSE) {
            $link = $this->SiteLink . '?' . $this->PageGET . '=';
        } else {
            $link = $this->SiteLink . '&' . $this->PageGET . '=';
        }

        $anterior = '';
        if ($this->PaginaAtual > 1) {
            $anterior = '<A HREF="'.$link.($this->PaginaAtual - 1).'" CLASS="'.$this->ClasseNavegadoresTxt.'">'.$this->TextoAnterior.'</A> ';
        }
		
		else $anterior = "<label CLASS=\"$this->ClasseNavdisabled\">".$this->TextoAnterior.'</label> ';
		
   
        $primeira = '';
        if (($this->PaginaAtual - ($this->NumPgLaterais + 1) > 1) && ($this->UltimaPagina > ($this->NumPgLaterais * 2 + 2))) {
            $primeira = '<A HREF="'.$link.'1" CLASS="'.$this->ClasseNavegadores.'">1</A> <LABEL CLASS="'.$this->ClasseSeparadores.'">'.$this->TextoSeparador.'</LABEL> ';
            $dec = $this->NumPgLaterais;
        } else {
            $dec = $this->PaginaAtual - 1;
      
        }

        $ultima = '';
        if (($this->PaginaAtual + ($this->NumPgLaterais + 1) < $this->UltimaPagina) && ($this->UltimaPagina > ($this->NumPgLaterais * 2 + 2))) {
            $ultima = '<LABEL CLASS="'.$this->ClasseSeparadores.'">'.$this->TextoSeparador.'</LABEL> <A HREF="'.$link.$this->UltimaPagina.'" CLASS="'.$this->ClasseNavegadores.'">'.$this->UltimaPagina.'</A>';
            $inc = $this->NumPgLaterais;
        } else {
            $inc = $this->UltimaPagina - $this->PaginaAtual;
        }

        if ($dec < $this->NumPgLaterais) {
            $x = $this->NumPgLaterais - $dec;
            while ($this->PaginaAtual + $inc < $this->UltimaPagina && $x > 0) {
                $inc++;
                $x--;
            }
        }
      
        if ($inc < $this->NumPgLaterais) {
            $x = $this->NumPgLaterais - $inc;
            while ($this->PaginaAtual - $dec > 1 && $x > 0) {
                $dec++;
                $x--;
            }
        }

        $atual = '';
        for ($x = $this->PaginaAtual - $dec; $x <= $this->PaginaAtual + $inc; $x++) {
            if ($x == $this->PaginaAtual) {
                $atual .= '<LABEL CLASS="'.$this->ClassePaginaAtual.'">'.$x.'</LABEL> ';
            } else {
                $atual .= '<A HREF="'.$link.$x.'" CLASS="'.$this->ClasseNavegadores.'">'.$x.'</A> ';
            }
        }

        $proxima = '';
        if ($this->PaginaAtual < $this->UltimaPagina) {
            $proxima = ' <A HREF="'.$link.($this->PaginaAtual + 1).'" CLASS="'.$this->ClasseNavegadoresTxt.'">'.$this->TextoProxima.'</A>';
        }
		else $proxima = '<label CLASS="barNav_disabled">'.$this->TextoProxima.'</label> ';
		

        return $this->HTMLPaginacao = "<div id=\"containerPaginacao\">".$anterior.$primeira.$atual.$ultima.$proxima."</div>";
    }
}
?>
