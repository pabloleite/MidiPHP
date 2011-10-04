/* ### BIBLIOTECA DE CÓDIGOS JAVASCRIPT ###############################################################################################################
function speedTest() - dá um alert na tela informando o tempo de carregamento do script. Usar no final da página.
function inputVal(obj) - apaga o valor de obj (input) caso este valor seja o padrão. Uso: onFocus="inputVal(this);" onBlur="inputVal(this,2);"
function mostraFlash(src, larg, alt, wmode) - insere uma animação flash na tela, src=local+nome_do_flash.swf, larg/alt=largura/altura em pixel, wmode=transparent se deseja que fique com o fundo transparente
function limpaForm(form) - solicita ao usuario se ele deseja realmente limpar os valores preenchidos do formulário. Uso: onclick="return limpaForm(this.form);"
function highlite(obj,type) - altera o className e ID do objeto passado como parametro e baseado no type. Uso: onmouseover="highlite(this,1);" onmouseout="highlite(this,-1);" onfocus="highlite(this,2);" onblur="highlite(this,-2);"
prototype trim() - remove espaços em branco de uma string, uso: obj.value.trim()
prototype replaceAll(findstr,newstr) - substitue todas ocorrencias de uma string por outra. uso: var result=obj.value.replaceAll('á','a')
function innerTxt(obj) - retorna o mesmo que a funcao javascript innerText, porém este funciona no IE/Mozilla
function addEvent(object,evType,func,useCapture) - cria um event handler para o objeto, funciona no IE/Mozilla, (objeto/evento/funcao/parametros). exemplo: var func=function(){alert("minha funcao");}addEvent(document, "click", func);
function removeEvent=function(o,e,f,s) - remove um evento previamente definido atraves da funcao addEvent(), para remover a função, os parametros passados para esta função devem ser os mesmos daqueles passados em addEvent, Exemplo: removeEvent(document, "click", func);
function saveCookie(cookieName,cookieValue,days) - grava o cookie no computador do usuário.
function readCookie(cookieName) - retorna o valor do cookie (cookieName) salvo no computador do usuário.
function getMousePos(e) - retorna um array de 2 elementos(0 e 1, x e y respectivamente) com as coordenadas do mouse na tela do computador. Uso: document.onmousemove=getMousePos;
function checkEmail(string) - retorna true se a string passada for um email válido, caso contrário, retorna false.
function untag(string,tag) - versão da funcao untag do php, retorna um array com o conteudo que está entre <tag> e </tag>
function objPos(obj) - encontra a posição do objeto na tela e retorna um array[x,y]
function str_pad(input,pad_length,pad_string,pad_type) - Preenche uma string para um certo tamanho com outra string, mesma coisa do str_pad do PHP
function getElementsByClassName(classname,[tag, mixed [childOf]]) - retorna um array com os objetos com o className, que seja da mesma tag, e que seja filho de childOf. Exemplo: botoes=getElementsByClassName('BTmove','img',pai);
function docHeight() - retorna o tamanho máximo da janela. ideal para setar a altura de um objeto como se fosse a 100%.
function iframeResize() - ajusta o tamanho do iframe atual dependendo do tamanho do conteudo da janela. Necessita da função docHeight() e o iframe, deve ter um id único na janela parent. Chamar esta funcao no final do iframe
function checaCampos(form) - verifica se os campos do formulario são requeridos ou não antes de enviar o form.
function querystring(variavel) - mesmo que $_GET[variavel]
function formataReais(this,'.',',',event)) - formata um input com a mascara de reais
function goodchars(event,chars)  //necessita da funcao getkey(). Não permite que o usuário escreva os caracteres que não estejam em 'chars'. USO: onKeyPress="return goodchars(event,'0123456789,.')"
function validarCPF(cpf)  //retorna true caso o cpf informado seja válido. (somente caracteres)
#####################################################################################################################################################*/
var start=(new Date()).getTime();
function speedTest(){
	var duration=(new Date()).getTime()-start;
	alert(duration);
}

function mostraFlash(src, larg, alt, wmode){
	var flash = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="'+ larg +'" height="'+ alt +'">';
	flash += '<param name="movie" value="'+ src +'" />';
	flash += '<param name="allowScriptAccess" value="sameDomain" />';
	flash += '<param name="menu" value="false" />';	
	flash += '<param name="wmode" value="'+ wmode +'" />';	
	flash += '<embed src="'+ src +'" pluginspage="http://www.macromedia.com/go/getflashplayer" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" width="'+ larg +'" height="'+ alt +'" menu = "false" wmode = "'+ wmode +'"></embed>';
	flash += '</object>';	
	
	document.write(flash);
}
function popUp(URL, popupWidth, popupHeight) {
	day = new Date();
	id = day.getTime();
	window.open(URL, id, 'toolbar=1,scrollbars=0,location=1,statusbar=1,menubar=0,resizable=0,width='+popupWidth+',height='+popupHeight+');');
}

function inputVal(obj,type){
	if(type==2){if(obj.value==''){obj.value=obj.defaultValue;}return;}
	if(obj.defaultValue==obj.value){obj.value=''};
}

function limpaForm(form){
	return confirm('Tem certeza de que deseja limpar os valores preenchidos acima?');
}

function highlite(obj,type){  //uso: onmouseover="highlite(this,1);" onmouseout="highlite(this,-1);" onfocus="highlite(this,2);" onblur="highlite(this,-2);"
	if(type==-1){obj.className=obj.className.replace(/inputOver/,'');}
	else if(type==1){obj.className+=' inputOver';}
	else if(type==2){obj.id='inputOver';}
	else{obj.id='';obj.className=obj.className.replace(/inputOver/,'');}
}

function checkEmail(valor){
	if(valor==''){return true;}
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(valor)){return (true);}
	return (false);
}

function untag(string,tag){
	var preg=new RegExp('<'+tag+'>(.*?)</'+tag+'>','g');
	if(!preg.test(string)){return '';}
	var result=string.match(preg);
	for(var i=0;i<result.length;i++){
		result[i]=result[i].substring(tag.length+2,result[i].indexOf('</'+tag+'>'));
	}
	return result;
}

function objPos(obj){  //encontra a posicao do objeto na tela
	if(!obj){return false;}
	var zxclft,zxctop;
	zxclft=obj.offsetLeft;
	zxctop=obj.offsetTop;
	while(obj.offsetParent!=null){
		zxcpar=obj.offsetParent;
		zxclft+=zxcpar.offsetLeft;
		zxctop+=zxcpar.offsetTop;
		obj=zxcpar;
	}
	return [zxclft,zxctop];
}

function str_pad(input,pad_length,pad_string,pad_type){
	input=String(input);
	pad_string=pad_string!=null?pad_string:" ";
	if(pad_string.length>0){
		var padi=0;
		pad_type=pad_type!=null?pad_type:"STR_PAD_RIGHT";
		pad_length=parseInt(pad_length);
		switch(pad_type){
			case "STR_PAD_BOTH":
				input=str_pad(input,input.length+Math.ceil((pad_length-input.length)/2.0),pad_string,"STR_PAD_RIGHT");
			//break;  // kein break!
			case "STR_PAD_LEFT":
				var buffer="";
				for(var i=0,z=pad_length-input.length;i<z;++i){
					buffer+=pad_string.charAt(padi);  //[padi] IE 6.x bug
					if(++padi==pad_string.length){padi=0;}
				}
				input=buffer+input;
			break;
			default:
				for(var i=0,z=pad_length-input.length;i<z;++i){
					input+=pad_string.charAt(padi);
					if(++padi==pad_string.length){padi=0;}
				}
			break;
		}
	}
	return input;
}
 function getElementsById(sId)
 {
    var outArray = new Array();	
	if(typeof(sId)!='string' || !sId)
	{
		return outArray;
	};
	
	if(document.evaluate)
	{
		var xpathString = "//*[@id='" + sId.toString() + "']"
		var xpathResult = document.evaluate(xpathString, document, null, 0, null);
		while ((outArray[outArray.length] = xpathResult.iterateNext())) { }
		outArray.pop();
	}
	else if(document.all)
	{
		
		for(var i=0,j=document.all[sId].length;i<j;i+=1){
		outArray[i] =  document.all[sId][i];}
		
	}else if(document.getElementsByTagName)
	{
	
		var aEl = document.getElementsByTagName( '*' );	
		for(var i=0,j=aEl.length;i<j;i+=1){
		
			if(aEl[i].id == sId )
			{
				outArray.push(aEl[i]);
			};
		};	
		
	};
	
	return outArray;
 }

function getElementsByClassName(classname,tag,childOf){
	if(!tag){tag="*";}
	if(typeof(childOf)=='string'){childOf=document.getElementById(childOf);}
	var anchs=document.getElementsByTagName(tag);
	var total_anchs=anchs.length;
	var regexp=new RegExp('\\b'+classname+'\\b');
	var class_items=new Array();
	
	for(var i=0;i<total_anchs;i++){ //Go thru all the links seaching for the class name
		var this_item=anchs[i];
		if(regexp.test(this_item.className)){
			if(childOf){
				pai=this_item.parentNode;
				if(!pai){continue;}
				while(pai!=null){
					if(pai==childOf){class_items.push(this_item);break;}
					pai=pai.parentNode;
				}
			}else{class_items.push(this_item);}
		}
	}
	return class_items;
}

/*#####################################################################################################################################################*/
//VARIAVEIS IMPORTANTES
var IE=document.all?true:false;

//FUNCTION PROTOTYPES
String.prototype.trim=function(){  //trim leading or trailing whitespace and extra spaces
	return this.replace(/^\s*/, "").replace(/\s*$/, "").replace(/\s{2,}/, " ");
}

String.prototype.replaceAll=function(findstr,newstr){  //replace all occurences of string
	return this.replace(eval('/'+findstr+'/gi'),newstr);  //case insensitive
}

//OTHER FUNCTIONS
function innerTxt(obj){  //retorna o mesmo que innerText - para IE/Mozilla
	if(!obj){return '';}
	return (obj.innerText)?obj.innerText:obj.textContent;
}

//EXEMPLOS:var func=function(){alert("minha funcao");}addEvent(document, "click", func);
addEvent=function(o,e,f,s){  //adiciona evento ao objeto IE/FF - objeto/evento/funcao/parametros
	var r=o[r="_"+(e="on"+e)]=o[r] || (o[e]?[[o[e],o]]:[]),a,c,d;
	r[r.length]=[f,s || o],o[e]=function(e){
		try{
			(e=e || event).preventDefault || (e.preventDefault=function(){e.returnValue = false;});
			e.stopPropagation || (e.stopPropagation=function(){e.cancelBubble=true;});
			e.target || (e.target=e.srcElement || null);
			e.key=(e.which+1 || e.keyCode+1)-1 || 0;
		}catch(f){}
		for(d=1, f=r.length; f;r[--f] && (a=r[f][0],o=r[f][1],a.call?c=a.call(o,e):(o._=a,c=o._(e),o._=null),d &=c!==false));
		return e=null,!!d;
	}
};

//EXEMPLOS:removeEvent(document, "click", func);
removeEvent=function(o,e,f,s){  //remove evento do objeto IE/FF
	for(var i=(e=o["_on"+e] || []).length;i;)
	if(e[--i] && e[i][0]==f && (s || o)==e[i][1])
	return delete e[i];
	return false;
};

function addEvent(object,evType,func,useCapture){  //attacha um evento a um elemento IE/MOZILLA
	useCapture=true;
	if(object.addEventListener){
		object.addEventListener(evType,func,useCapture);
	}else if(object.attachEvent){
		object.attachEvent("on"+evType,func);
	}
}

function saveCookie(cookieName,cookieValue,days){
	var date=new Date();
	var expires;
	if(days){
		date.setTime(date.getTime()+(days*24*60*60*1000));
		expires='EXPIRES='+date.toGMTString()+';';
	}
	if(document.cookie=cookieName+"="+cookieValue+";"+expires+"PATH=/"){
		return true;
	}
	return false;
}

function readCookie(cookieName){
	var cookieString=document.cookie;
	var index1=cookieString.indexOf(cookieName);
	if(index1<0 || cookieName==''){return "";}
	var index2=cookieString.indexOf(';',index1);
	if(index2<0){index2=cookieString.length;}
	return unescape(cookieString.substring(index1+(cookieName.length+1),index2));
}

var mousePos=new Array();
function getMousePos(e){  //pega a posicao do mouse
	if(IE){
		mousePos[0]=event.clientX+document.body.scrollLeft;
		mousePos[1]=event.clientY+document.body.scrollTop;
	}else{
		mousePos[0]=e.pageX;
		mousePos[1]=e.pageY;
	}
	mousePos[0]=Math.max(0,mousePos[0]);
	mousePos[1]=Math.max(0,mousePos[1]);
	return ([mousePos[0],mousePos[1]]);
}

function docHeight(){
	var maxHeight=0;
	var parent=document.body;
	
	while(parent){
		maxHeight=Math.max(maxHeight,isNaN(parent.offsetHeight)?0:parent.offsetHeight);
		parent=parent.parentNode;
	}
	return maxHeight;
}

function isVisible(obj){  //verifica se o elemento está visivel
	if(!obj.parentNode){return false;}
	while(obj.parentNode!=null){
		if(obj.style.display.toLowerCase()=='none' || obj.style.visibility.toLowerCase()=='hidden'){return false;}
		obj=obj.parentNode;
	}
	return true;
}

function checkEmail(valor){
	if(valor==''){return true;}
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(valor)){return (true);}
	return (false);
}

function checkdate(d,m,y)
{
	if(!IsNumeric(d) || !IsNumeric(m) || !IsNumeric(y) || d.length<2 || m.length<2 || y.length<4){return false;}
	var yl=1900; // least year to consider
	var ym=2500; // most year to consider
	if (m<1 || m>12) return(false);
	if (d<1 || d>31) return(false);
	if (y<yl || y>ym) return(false);
	if (m==4 || m==6 || m==9 || m==11)
	if (d==31) return(false);
	if (m==2)
	{
		var b=parseInt(y/4);
		if (isNaN(b)) return(false);
		if (d>29) return(false);
		if (d==29 && ((y/4)!=parseInt(y/4))) return(false);
	}
	return(true);
}

function IsNumeric(sText)
{
	var ValidChars = "0123456789";
	var IsNumber=true;
	var Char;
	for (i = 0; i < sText.length && IsNumber == true; i++) 
	{ 
		Char = sText.charAt(i); 
		if (ValidChars.indexOf(Char) == -1) 
		{
			IsNumber = false;
		}
	}
	return IsNumber;
}

function isHour(value){
	if(value==''){alert('Favor digite uma hora válida.');return false;}
	var splitted=value.split(':');
	var hora=splitted[0];
	var minuto=splitted[1];
	if(!IsNumeric(hora) || !IsNumeric(minuto) || (hora==0 || hora>=24) || (minuto==0 || minuto>=60)){return false;}
	return true;
}

function hideValidateError() {
	if ($("#validateFormError")) {
		$("#validateFormError").hide("fast");
	}
}
function returnValidateError(element)
{
	if (document.getElementById('validateFormError')!=null)
	{
		$("#validateFormError").html("<p style=\"text-align:center; color:#990000; font-weight:bold;\">Atenção - Há campos em branco ou inválidos.<br/>"+element.title+"</p>");
		$("#validateFormError").show('normal');
		element.focus();
	} else {
		$("#validateFormError").html("<p style=\"text-align:center; color:#990000; font-weight:bold;\">Atenção - Há campos necessarios em branco ou inválidos.</p>");
		element.focus();
	}
}

function validateForm(form){
	if(!form)
	{
		return false;
		}
	hideValidateError();
	var element,enviar=true;
	 for(var i=0;i<form.elements.length;i++){
		element=form.elements[i];
		if(element.className.indexOf('required')>=0){  //campo requerido
		  if(!isVisible(element)){continue;}
			if(element.type.toUpperCase()=='CHECKBOX'){  //se for um checkbox
				checkboxes=document.getElementsByName(element.name);
				for(var c=0;c<checkboxes.length;c++){
					if(checkboxes[c].checked){break;}
				}
				if(c<checkboxes.length){continue;}
				returnValidateError(element);
				return false;
			}
			if(element.value.length<=0){  //nao preenchido
				if(element.type.toUpperCase().indexOf('SELECT')>=0){
					returnValidateError(element);
				}else{
					if(!isVisible(element)){continue;}
					returnValidateError(element);
				}
				enviar=false;
				break;
			}
		}
		if(element.className.indexOf('email')>=0){  //campo tipo email
			if(!checkEmail(element.value.trim())){
				returnValidateError(element);
				enviar=false;
				break;
				
			}
		}
		if(element.className.indexOf('hour')>=0){  //campo tipo hora HH:mm:ss
			if(!isHour(element.value.trim())){
				returnValidateError(element);
				enviar=false;
				break;
			}
		}
		if(element.className.indexOf('number')>=0){  //campo tipo numero
			if(!IsNumeric(element.value.trim())){
				returnValidateError(element);
				enviar=false;
				break;
			}
		}
		if(element.className.indexOf('cpf')>=0){  //campo tipo cpf
			if(!validarCPF(element.value.trim())){
				returnValidateError(element);
				enviar=false;
				break;
			}
		}
		if(element.className.indexOf('day')>=0){  //campo tipo dia
			if(!IsNumeric(element.value.trim())&&!isDay(element.value.trim())){
				returnValidateError(element);
				enviar=false;
				break;
			}
		}
		if(element.className.indexOf('senha')>=0){  //campo tipo senha
			if(form.pwd1.value!=form.pwd2.value){
			alert('Senhas não conferem, favor digite-as novamente.');
			form.pwd1.value='';
			form.pwd2.value='';
			form.pwd1.focus();
			returnValidateError(element);
			enviar=false;
			break;
			}
		}
	}
	return enviar;
}


function querystring(variavel){
	var variaveis=location.search.replace(/\x3F/,"").replace(/\x2B/g," ").split("&")
	var nvar;
	if(variaveis!=""){
		var qs=[];
		for(var i=0;i<variaveis.length;i++){
			nvar=variaveis[i].split("=");
			qs[nvar[0]]=unescape(nvar[1]);
		}
		if(variavel){return qs[variavel];}
		return variaveis;
	}
	return null
}

function iframeResize(){
	var thisIframe=window.parent.document.getElementsByName(this.window.name)[0];
	thisIframe.style.height=docHeight()+'px';
}

function formataReais(fld,milSep,decSep,e){  //USO: onKeyPress="return(formataReais(this,'.',',',event))"
	var sep=0,key='',i=j=0,len=len2=0,strCheck='0123456789',aux=aux2='';
	var whichCode=(window.Event)?e.which:e.keyCode;
	if(whichCode==13){return true;}
	key=String.fromCharCode(whichCode);  //Valor para o código da Chave
	if(strCheck.indexOf(key)==-1)return false;  //Chave inválida
	len=fld.value.length;
	for(i=0;i<len;i++)
		if((fld.value.charAt(i)!='0')&&(fld.value.charAt(i)!=decSep))break;
	aux='';
	for(;i<len;i++)
		if(strCheck.indexOf(fld.value.charAt(i))!=-1)aux+=fld.value.charAt(i);
	aux+=key;
	len=aux.length;
	if(len==0)fld.value='';
	if(len==1)fld.value='0'+decSep+'0'+aux;
	if(len==2)fld.value='0'+decSep+aux;
	if(len>2){
		aux2='';
		for(j=0,i=len-3;i>=0;i--){
			if(j==3){
				aux2+=milSep;
				j=0;
			}
			aux2+=aux.charAt(i);
			j++;
		}
		fld.value='';
		len2=aux2.length;
		for(i=len2-1;i>=0;i--)fld.value+=aux2.charAt(i);
		fld.value+=decSep+aux.substr(len-2,len);
	}
	return false;
}
















//GOODCHARS
function getkey(e){
	if(window.event)return window.event.keyCode;
	else if(e)return e.which;
	else return null;
}

function goodchars(e,goods){
	var key,keychar;
	key=getkey(e);
	if(key==null)return true;
	
	//get character
	keychar=String.fromCharCode(key);
	keychar=keychar.toLowerCase();
	goods=goods.toLowerCase();
	
	//check goodkeys
	if(goods.indexOf(keychar)!=-1)return true;
	
	//control keys
	if(key==null||key==0||key==8||key==9||key==13||key==27)return true;
	return false;
}

function onlyNumbers(string) {
	var numbersStr = "";
	for(var i=0;i < string.length; ++i) {
		   var char = string.charAt(i); 
		   if((char >= "0") && (char <= "9")){
				numbersStr += char;
		   }
	}
	return numbersStr;
}

function validarCPF(cpf){
	cpf = onlyNumbers(cpf);
	
   if(cpf.length != 11 || cpf == "00000000000" || cpf == "11111111111" ||
	  cpf == "22222222222" || cpf == "33333333333" || cpf == "44444444444" ||
	  cpf == "55555555555" || cpf == "66666666666" || cpf == "77777777777" ||
	  cpf == "88888888888" || cpf == "99999999999"){
	  return false;
   }

   soma = 0;
   for(i = 0; i < 9; i++)
   	 soma += parseInt(cpf.charAt(i)) * (10 - i);
   resto = 11 - (soma % 11);
   if(resto == 10 || resto == 11)
	 resto = 0;
   if(resto != parseInt(cpf.charAt(9))){
	 /*window.alert("CPF inválido. Tente novamente.");*/
	 return false;
   }
   soma = 0;
   for(i = 0; i < 10; i ++)
	 soma += parseInt(cpf.charAt(i)) * (11 - i);
   resto = 11 - (soma % 11);
   if(resto == 10 || resto == 11)
	 resto = 0;
   if(resto != parseInt(cpf.charAt(10))){return false;}
   return true;
}