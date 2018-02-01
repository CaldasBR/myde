$(document).ready(function(){
    busca_userid();
});


function trataDados(i){
	jsonData[i] = JSON.parse(ajax[i].responseText);
	if(jsonData[i].erro === 1 && jsonData[i].opt === 'Toast'){
		Materialize.toast(jsonData[i].msg, 4000);
	}else{
        switch (jsonData[i].funcao){
            case "redir_aut_mercadopago":
                switch(jsonData[i].opt) {
                    case "Distribuidor Vinculado!":
                        window.location.href = "http://desenv.queromarita.com.br/pagamento.html";
                    break;
                }
            break;
            case "busca_user_id":
                switch(jsonData[i].opt) {
                    case "achei_id":
                        autorizacao(jsonData[i].msg);
                    break;
                }
            break;
        }
    }
}
                  
function busca_userid(){
	var url="http://desenv.queromarita.com.br/bg_autorizacao.php";
    var parametros = {
        fn: "busca_user_id",
    };
	requisicaoHTTP("POST",url,parametros,true,"busca_user_id");
}

function autorizacao(id){
    var dest = "https://auth.mercadopago.com.br/authorization?client_id=7612629650074174&response_type=code&platform_id=mp&redirect_uri=https%3A%2F%2Fqueromarita.com.br/bg_redirect.php";
    window.location = dest;
}