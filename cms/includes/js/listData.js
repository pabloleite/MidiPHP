// JavaScript Document

function excludeMultiple(form) {
	var nChecked = 0;
	for (var i=0; i<form.elements.length; i++) {
		var checkbox = document.getElementById('multipleExclusion_'+i);
		if (checkbox) {
			if (checkbox.checked){
				nChecked++;
			}
		}
	}
	if (nChecked==0) {
		alert("Selecione ao menos um registro para excluir.");
	} else {
		if (nChecked == 1) {
			var confirmBox = confirm("Deseja realmente excluir o registro selecionado?");
		} else {
			var confirmBox = confirm("Deseja realmente excluir os "+nChecked+" registros selecionados?");
		}
		
		if (confirmBox) {
			form.submit();
		}
	}
}
function checkUncheckAll(form, checkboxName) {
	for (var i=0; i<form.elements.length; i++) {
		var checkbox = document.getElementById('multipleExclusion_'+i);
		if (checkbox.name = checkboxName) {
			if (checkbox.checked) {
				checkbox.checked=false;
			} else {
				checkbox.checked=true;
			}
		}
	}
}
function enlargeImage() {
	day = new Date();
	id = day.getTime();
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=1,height=1');");
}
function gotoUrl(url) {
	window.location = url;
}