var fx_bread = new breadcrumb();
$(document).ready(function(){
    fx_bread.atualizar();
    fx_bread.inserir('indicados.html','Convidar Amigos');
    //Popup  modal pagamento
    $(".dropdown-button").dropdown();
    $(".button-collapse").sideNav();
    $('.collapsible').collapsible();
    carregar_menu();
    carregar_indicados();
    carregar_chat();
    carregar_rodape();
});

//Máscara Celular
$("#celular").keyup(function(){
    document.getElementById("celular").value = celular(document.getElementById("celular").value);
});

//Start Validação Celular
$("#celular").focusout(function(){
    valida_telefone(document.getElementById("celular").value);
});
function carregar_menu(){
    var url="http://desenv.queromarita.com.br/bg_menu.php";
    var parametros = {
        fn: "le_menu"
    };
    requisicaoHTTP("GET",url,parametros,true,"le_menu");
}

function carregar_rodape(){
    var url="http://desenv.queromarita.com.br/bg_footer.php";
    var parametros = {
        fn: "le_rodape"
    };
    requisicaoHTTP("GET",url,parametros,true,"le_rodape");
}

//Função que trata a resposta do ajax
function trataDados(i){
	jsonData[i] = JSON.parse(ajax[i].responseText);
	if(jsonData[i].erro === 1 && jsonData[i].opt === 'Toast'){
		Materialize.toast(jsonData[i].msg, 4000);
	}else{
        switch(jsonData[i].funcao){
            case "indicacao":
                switch(jsonData[i].opt){
                    case "exibir_indicados":
                        var indicados = document.getElementById("tabela_indicados");
                        indicados.innerHTML = jsonData[i].msg;
                    break;
                    case "expulsa":
                        window.location.replace('http://desenv.queromarita.com.br/');
                    break;
                    case "Toast":
                        Materialize.toast(jsonData[i].msg, 4000);
                    break;
                    case "cadastro_efetuado":
                        Materialize.toast(jsonData[i].msg, 4000);
                        carregar_indicados();
                    break;
                }
            break;
            case 'menu':
                var menu = document.getElementById("barra_menu");
                menu.innerHTML = jsonData[i].msg;
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $('.collapsible').collapsible();
            break;
            case 'rodape':
                var rodape = document.getElementById("rodape");
                rodape.innerHTML = jsonData[i].msg;
            break;
            case 'chat':
                switch(jsonData[i].opt){
                    case 'exibir_chat':
                        exibe_chat(jsonData[i].msg.id_chat,jsonData[i].msg.usr_nome, jsonData[i].msg.usr_email,jsonData[i].msg.hash);
                    break;
                }
            break;
		}
	}
}

function cadastrar_unico(){
    erroValidacao = 0;
	var nome = document.getElementById("nome").value;
    if(nome.length < 2){
        erroValidacao = 1;
    }
    var email = document.getElementById("email").value;
    if(email.length < 6){
        erroValidacao = 1;
    }
    var celular = document.getElementById("celular").value;
    if(erroValidacao==0){
        var url="http://desenv.queromarita.com.br/bg_indicados.php";
        var parametros = {
            fn: "cadastrar_unico",
            nome: nome,
            email: email,
            celular: celular,
        };
        requisicaoHTTP("GET",url,parametros,true,"cadastrar_unico");
    }else{
        Materialize.toast("Por favor, verifique os campos da indicação.", 4000);
    }
}

function carregar_indicados(){
    var url="http://desenv.queromarita.com.br/bg_indicados.php";
    var parametros = {
        fn: "carregar_indicados",
    };
    requisicaoHTTP("GET",url,parametros,true,"carregar_indicados");
}

function carregar_chat(){
    var url="http://desenv.queromarita.com.br/bg_chat.php";
    var parametros = {
        fn: "carregar_chat"
    };
    requisicaoHTTP("GET",url,parametros,true,"carregar_chat");
}

function exibe_chat(id,usr_nome,usr_email,hash){
    
    var v1=document.createElement("script"),v0=document.getElementsByTagName("script")[0];
    var visitante = "var Tawk_API=Tawk_API||{};Tawk_API.visitor = { name: '" + usr_nome + "',  email: '" + usr_email + "'};";
    v1.innerText = visitante;
    v0.parentNode.insertBefore(v1,v0);
    
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[1];
        s1.async=true;
        s1.src='https://embed.tawk.to/'+id+'/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
}