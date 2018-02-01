var fx_bread = new breadcrumb();
$(document).ready(function () {
    fx_bread.atualizar();
    exibir_detalhes();
    $('.modal').modal();
    $('#modal_carrinho_loja').modal('open');
    $('#modal_carrinho_loja').modal('close');
    $(".dropdown-button").dropdown();
    $(".button-collapse").sideNav();
    $('.collapsible').collapsible();
    carregar_menu();
    carregar_chat();
    carregar_rodape();
});


function carregar_menu(){
    var url="https://queromarita.com.br/bg_menu.php";
    var parametros = {
        fn: "le_menu"
    };
    requisicaoHTTP("GET",url,parametros,true,"le_menu");
}

function carregar_rodape(){
    var url="https://queromarita.com.br/bg_footer.php";
    var parametros = {
        fn: "le_rodape"
    };
    requisicaoHTTP("GET",url,parametros,true,"le_rodape");
}

function verificar_sacola(){
    var url="https://queromarita.com.br/bg_sacola__carregar_sacola.php";
    var parametros = {
        fn: "outros"
    };
    requisicaoHTTP("GET",url,parametros,true,"sacola__carregar_sacola");
}

function voltar_compra(){
    Materialize.toast("Seu produto já está na sua sacola!", 4000);
    window.location.replace('https://queromarita.com.br/loja.html');

}
function direcionar_carrinho(){
    window.location.replace('https://queromarita.com.br/sacola.html');
}
function exibir_distribuidor() {

    id = 1;
        url = "https://queromarita.com.br/bg_loja__carregar_dados_dist.php";
        parametros = {
            id: id
        }
        requisicaoHTTP("GET",url,parametros,true,"produto_detalhe__detalhar");
}
function exibir_detalhes() {
	var   id, url, parametros;
    id = buscar_parametro('id');
	if (id !== "") {

        url = "https://queromarita.com.br/bg_produto_detalhe__detalhar.php";
        parametros = {
            id: id
        };
        requisicaoHTTP("GET",url,parametros,true,"produto_detalhe__detalhar");
	}
}
function comprar(){
	var id = buscar_parametro('id');
    var qtde = document.getElementById("qtde").value*1;
	if (id !== "") {
		var url="https://queromarita.com.br/bg_produto_detalhe__comprar.php";
        var parametros ={
            id: id,
            qtde: qtde
        };
        requisicaoHTTP("GET",url,parametros,true,"produto_detalhe__comprar");
	}
}

/*function calcular_frete(){
    var id = buscar_parametro('id');
    var qtde = document.getElementById("qtde").value*1;
    var cep = apenasNumeros(document.getElementById("cep").value);
    var url="https://queromarita.com.br/bg_consulta_frete.php";
    var parametros ={
            id: id,
            qtde: qtde,
            cep: cep,
            fn: 'produto_detalhe'
        };
    requisicaoHTTP("GET",url,parametros,true,"consultar_frete");
}*/

//Função que trata a resposta do ajax
function trataDados(i){
	jsonData[i] = JSON.parse(ajax[i].responseText);
	if (jsonData[i].erro === 1) {
		Materialize.toast(jsonData[i].msg, 4000);
	}else{
        switch(jsonData[i].funcao){
            case 'verificar_carrinho':
                switch(jsonData[i].opt){
                    case "direcionar_sacola":
                        window.location.replace('https://queromarita.com.br/sacola.html');
                    break;
                }
            break;
            case "produto_detalhe":
                switch(jsonData[i].opt){
                    case "produto_detalhe__detalhar":
                        document.getElementById("detalhe_img").src = jsonData[i].msg["imagem1"];
                        document.getElementById("detalhe_titulo").innerText   = jsonData[i].msg["titulo"];
                        fx_bread.inserir('produto_detalhe.html?id='+buscar_parametro('id'),jsonData[i].msg["titulo"]);
                        document.getElementById("detalhe_texto1").innerText = jsonData[i].msg["detalhe_texto1"];
                        document.getElementById("detalhe_texto2").innerText = jsonData[i].msg["detalhe_texto2"];
                        document.getElementById("detalhe_preco").innerText = "R$ "+jsonData[i].msg["detalhe_preco"].replace(".", ",");
                        document.getElementById("titulo_desc").innerText   = jsonData[i].msg["titulo"];
                        document.getElementById("detalhe_texto3").innerText = jsonData[i].msg["detalhe_texto3"];
                        document.getElementById("tb_nutri").src = jsonData[i].msg["imagem2"];
                        document.getElementById("modo_preparo").src = jsonData[i].msg["imagem3"];

                        document.getElementById("meta_url").setAttribute("content",window.location.href);
                        document.getElementById("meta_title").setAttribute("content",jsonData[i].msg["titulo"]);
                        document.getElementById("meta_description").setAttribute("content",jsonData[i].msg["detalhe_texto1"]);
                        document.getElementById("meta_image").setAttribute("content",'https://queromarita.com.br/'+jsonData[i].msg["imagem1"]);
                        document.getElementById("fb_like").setAttribute("data-href",window.location.href);
                        document.getElementById("fb_comments").setAttribute("data-href",window.location.href);


                        <!--Habilitando Facebook -->
                        (function(d, s, id) {
                          var js, fjs = d.getElementsByTagName(s)[0];
                          if (d.getElementById(id)) return;
                          js = d.createElement(s); js.id = id;
                          js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.10&appId=384130268655785";
                          fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));
                        break;
                    case "produto_detalhe__comprar":
                        $('#modal_carrinho_loja').modal('open');
                        break;
                   /* case "produto_detalhe__calcular_frete":
                        document.getElementById("valor_frete").innerHTML   = jsonData[i].msg;
                        exibir_apagar("valor_frete","block");

                        break;  */
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

function carregar_chat(){
    var url="https://queromarita.com.br/bg_chat.php";
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
