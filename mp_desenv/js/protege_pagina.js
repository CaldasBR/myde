var ajaxProtege=new Array();
var jsonDataProtege=new Array();
var dadosUsuario = '';

// ------- cria o objeto e faz a requisição -------
function requisicaoHTTPProtege(tipo,url,dados,assinc,i){
	//Contador i indica a função que está solicitando a requisiçãohttp
	if(window.XMLHttpRequest){// Mozilla, Safari,...
		ajaxProtege[i] = new XMLHttpRequest();
	}
	else if (window.ActiveXObject){	// IE
		ajaxProtege[i] = new ActiveXObject("Msxml2.XMLHTTP");
		if (!ajaxProtege[i]) {
			ajaxProtege[i] = new ActiveXObject("Microsoft.XMLHTTP");
		}
    }
	if(ajaxProtege[i])	// iniciou com sucesso
			iniciaRequisicaoProtege(tipo,url,assinc,i);
	else{
		alert("Seu navegador não possui suporte a essa aplicação!");
    }
}

// ------- Inicializa o objeto criado e envia os dados (se existirem) -------
function iniciaRequisicaoProtege(tipo,url,bool,i){
	ajaxProtege[i].open(tipo,url,bool,i);
	ajaxProtege[i].setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
	ajaxProtege[i].overrideMimeType("text/XML");   /* usado somente no Mozilla */
	ajaxProtege[i].onload=function(){trataDadosProtege(i);}
	ajaxProtege[i].send(dadosUsuario);
}


function trataDadosProtege(i){
	jsonDataProtege[i] = JSON.parse(ajaxProtege[i].responseText);
	if(jsonDataProtege[i].funcao === "protege_pagina"){
        switch(jsonDataProtege[i].opt){
            case "expulsa":
                window.location.replace('http://desenv.queromarita.com.br/');
                break;
        }
    }
}

function protege_pagina(){
	var url="http://desenv.queromarita.com.br/bg_protege_pagina.php";
    var parametros = {};
	requisicaoHTTPProtege("GET",url,parametros,true,"proteger");
}

protege_pagina();