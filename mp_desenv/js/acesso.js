$(document).ready(function(){
    busca_usuario();
    if(buscar_parametro('fn') == 'naofui'){
        var url="http://desenv.queromarita.com.br/bg_acesso.php";
        var parametros = {
            fn: "naofui",
            
            id: buscar_parametro('id')
        };
        requisicaoHTTP("POST",url,parametros,true,"naofui");        
    }
});

//Função que trata a resposta do ajax
function trataDados(i){
	jsonData[i] = JSON.parse(ajax[i].responseText);
	if(jsonData[i].opt === 'Toast'){
		Materialize.toast(jsonData[i].msg, 4000);
	}else{
		switch(jsonData[i].funcao){
            case  "reset_senha":
                switch(jsonData[i].opt) {
                    case "sucesso":
                        Materialize.toast(jsonData[i].msg, 4000);
                        setTimeout(function (){
                                window.location.replace('http://desenv.queromarita.com.br/loja.html');
                        },3000);
                        
                    break;
                    case "mostra_nome":
                        document.getElementById("nome").innerText = jsonData[i].msg;
                    break;
                    case "expulsa":
                        window.location.replace('http://desenv.queromarita.com.br/');
                    break;
                }
            break;
        }
    }
}

function busca_usuario(){
    var url="http://desenv.queromarita.com.br/bg_acesso.php";
    var parametros = {
            fn: "busca_user",

            id: buscar_parametro('id'),
        };
        requisicaoHTTP("POST",url,parametros,true,"busca_user");
}

function reset_senha(){
    var url="http://desenv.queromarita.com.br/bg_acesso.php";
    
    if(md5(document.getElementById("senha_nova").value) == md5(document.getElementById("senha_confirm").value) && document.getElementById("senha_nova").value != ""){
        var parametros = {
            fn: "reset",
            
            id: buscar_parametro('id'),
            token: buscar_parametro('token'),
            nova: md5(document.getElementById("senha_nova").value),
        };
        requisicaoHTTP("POST",url,parametros,true,"reset");       
    }else{
        Materialize.toast("A senha nova não é igual a confimação, por favor verifique", 4000);
        document.getElementById("senha_nova").value = "";
        document.getElementById("senha_confirm").value = "";
        $('#senha_nova').focus();
    }
    
}
