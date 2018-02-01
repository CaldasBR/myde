var fx_bread = new breadcrumb();
$(document).ready(function(){
    fx_bread.atualizar();
    fx_bread.inserir('estoque.html','Produtos/Estoque');
    $(".dropdown-button").dropdown();
    $(".button-collapse").sideNav();
    $('.collapsible').collapsible();
    $('.modal').modal({dismissible: true});
    carregar_menu();
    ler_estoque();
    carregar_chat();
    carregar_rodape();
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

function verificar_sacola(){
    var url="http://desenv.queromarita.com.br/bg_sacola__carregar_sacola.php";
    var parametros = {
        fn: "outros"
    };
    requisicaoHTTP("GET",url,parametros,true,"sacola__carregar_sacola");
}
//Função que trata a resposta do ajax
function trataDados(i){
	jsonData[i] = JSON.parse(ajax[i].responseText);
	if(jsonData[i].erro === 1 && jsonData[i].opt === 'Toast'){
		Materialize.toast(jsonData[i].msg, 4000);
	}else{
        switch(jsonData[i].funcao){
            case 'verificar_carrinho':
                switch(jsonData[i].opt){
                    case "direcionar_sacola":
                        window.location.replace('http://desenv.queromarita.com.br/sacola.html');
                    break;
                }
            break;    
            case "estoque":
                switch(jsonData[i].opt){
                    case "mostra_estoque":
                        document.getElementById('conteudo_estoque').innerHTML = jsonData[i].msg;
                    break;
                    case "Toast":
                        Materialize.toast(jsonData[i].msg, 4000);
                        if(jsonData[i].opt2=='refresh'){
                            setTimeout(function (){
                                window.location.replace('http://desenv.queromarita.com.br/estoque.html');
                            },1500);
                        }
                    break;
                    case "expulsa":
                        window.location.replace('http://desenv.queromarita.com.br/');
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

function ler_estoque(){
    var url="http://desenv.queromarita.com.br/bg_estoque.php";
    var parametros = {
        fn: "ler_estoque",
    };
	requisicaoHTTP("POST",url,parametros,true,"ler_estoque");
}

function salvar(){
    var campos = document.body.getElementsByTagName("input");
    var parametros = {};
    for (var i = campos.length - 1; i >= 0; i--) {
        var campo = campos[i];
        if(campo.type == "checkbox"){
             parametros[campo.id] = campo.checked;
        }else{
            parametros[campo.id] = campo.value;   
        }
        //alert('Nome do campo: ' + campo.id)
    }
    parametros['fn'] = 'salvar_estoque';
    //console.log('Conteudo de parametros: '+JSON.stringify(parametros));
    var url="http://desenv.queromarita.com.br/bg_estoque.php";
    requisicaoHTTP("GET",url,parametros,true,"salvar_estoque");
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
