//###########################################################################
//							Biblioteca Ajax 2.0
// Extraída do livro: Web Interativa com Ajax e PHP
// Autor: Juliano Niederauer
// Adaptada por: Filipe Caldas
// Em: 18/04/2017
//###########################################################################

var ajax=new Array();
var dadosUsuario;
var jsonData=new Array();

// ------- cria o objeto e faz a requisição -------
function requisicaoHTTP(tipo,url,dados,assinc,i){
	//Contador i indica a função que está solicitando a requisiçãohttp
	if(window.XMLHttpRequest){// Mozilla, Safari,...
		ajax[i] = new XMLHttpRequest();
	}
	else if (window.ActiveXObject){	// IE
		ajax[i] = new ActiveXObject("Msxml2.XMLHTTP");
		if (!ajax[i]) {
			ajax[i] = new ActiveXObject("Microsoft.XMLHTTP");
		}
    }
	if(ajax[i])	// iniciou com sucesso
		dadosUsuario="";
		if(tipo.toUpperCase()=="GET"){
			criaQueryString(dados);
			if(url.slice(-1)!="?"){
				url = url + "?";	
			}
			if(dadosUsuario != ""){
				url = url + dadosUsuario;
			}
			iniciaRequisicao(tipo,url,assinc,i);
		}else if(tipo.toUpperCase()=="POST"){
			criaQueryString(dados);
			iniciaRequisicao(tipo,url,assinc,i);
		}else if(tipo.toUpperCase()=="FORM"){
			criaQueryStringForm(dados);
			iniciaRequisicao("POST",url,assinc,i);
		}
	else
		alert("Seu navegador não possui suporte a essa aplicação!");
}

// ------- Inicializa o objeto criado e envia os dados (se existirem) -------
function iniciaRequisicao(tipo,url,bool,i){
	ajax[i].open(tipo,url,bool,i);
	ajax[i].setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
	ajax[i].overrideMimeType("text/XML");   /* usado somente no Mozilla */
	ajax[i].onload=function(){$("#wait").css("display", "none");trataDados(i);}
	ajax[i].send(dadosUsuario);
}

// ------- Cria a string a ser enviada, formato campo1=valor1&campo2=valor2... -------
function criaQueryString(dados){
	var keys = Object.keys(dados);
	for (var i = 0; i < keys.length; i++) {
		if(i < keys.length-1){
			dadosUsuario += encodeURIComponent(keys[i]) + "=" + encodeURIComponent(dados[keys[i]])+"&";
		}else{
			dadosUsuario += encodeURIComponent(keys[i]) + "=" + encodeURIComponent(dados[keys[i]]);
		}		
	}
}
// ------- Cria a string a ser enviada, formato campo1=valor1&campo2=valor2... a partir de um formulário específico
function criaQueryStringForm(formName){
	var frm = document.getElementById(formName);
	var numElementos =  frm.elements.length;
	for(var i = 0; i < numElementos; i++)  {
		if(i < numElementos-1)  {
			dadosUsuario += frm.elements[i].name+"="+encodeURIComponent(frm.elements[i].value)+"&";
		} else {
			dadosUsuario += frm.elements[i].name+"="+encodeURIComponent(frm.elements[i].value);
		}
	}
}

// ------- Trata a resposta do servidor -------
function trataResposta(i){
	if(ajax[i].readyState == 4){
		if(ajax[i].status == 200){
			trataDados(i);  // criar essa função no seu programa
		} else {
			console.log("Problema na comunicação com o objeto XMLHttpRequest, veja a biblioteca Ajax.");
		}
	}
}