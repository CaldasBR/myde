$(document).ready(function(){
    //Popup  modal pagamento
    $(".dropdown-button").dropdown();
    $(".button-collapse").sideNav();
    $('.collapsible').collapsible();
    $('.modal').modal({dismissible: false});
    $('select').material_select();    
    $('.slider').slider({height: 240});
    carregar_menu();
    carregar_loja();
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

function carregar_loja(){
    var url="http://desenv.queromarita.com.br/bg_loja__carregar_loja.php";
    var parametros = {
        1:1
    };
    requisicaoHTTP("GET",url,parametros,true,"loja__carregar_loja");
}

function verificar_sacola(){
    var url="http://desenv.queromarita.com.br/bg_sacola__carregar_sacola.php";
    var parametros = {
        fn: "outros"
    };
    requisicaoHTTP("GET",url,parametros,true,"sacola__carregar_sacola");
}

//Máscara ao digitar CPF
$("#cpf").keyup(function() {
    document.getElementById("cpf").value = cpf_mask(document.getElementById("cpf").value);
});

//Máscara Celular
$("#celular").keyup(function(){
    document.getElementById("celular").value = celular(document.getElementById("celular").value);
});

//Start Validação Nome
$("#nome_completo").focusout(function(){
    var nome_array = valida_nome(document.getElementById("nome_completo").value);
    if(nome_array !== false){
        erroValidacao = 0;
    }
});

//Start Validação Celular
$("#celular").focusout(function(){
    valida_telefone(document.getElementById("celular").value);
});

//Start Validação CPF
$("#cpf").focusout(function(){
    valida_cpf(document.getElementById("cpf").value);
});

//Start Validação Senha
$("#senha_cadastro").focusout(function(){
    valida_senha(document.getElementById("senha_cadastro").value);
});

//Função que trata a resposta do ajax
function trataDados(i){
    var caixa=new Array();
    caixa[i] = document.getElementById('caixa');
	jsonData[i] = JSON.parse(ajax[i].responseText);
	if(jsonData[i].erro === 1 && jsonData[i].opt === 'Toast'){
		Materialize.toast(jsonData[i].msg, 4000);
	}else{
        switch(jsonData[i].funcao){
            case "login_myde":
                switch(jsonData[i].opt){
                    case "logado":
                        window.location.replace('http://desenv.queromarita.com.br/loja.html');
                        break;
                    case "expulsa":
                        window.location.replace('http://desenv.queromarita.com.br/');
                        break;
                }
            break;
            case "loja":
                switch(jsonData[i].opt){
                    case "loja__carregar_loja":
                        caixa[i].innerHTML=jsonData[i].msg;
                    break;
                    case "expulsa":
                        window.location.replace('http://desenv.queromarita.com.br/login.html');
                    break;
                }
            break;            
            case 'reset_senha':
                switch(jsonData[i].opt){
                    case "Toast":
                        Materialize.toast(jsonData[i].msg, 4000);
                    break;
                }
            break;
            case 'verificar_carrinho':
                switch(jsonData[i].opt){
                    case "direcionar_sacola":
                        window.location.replace('http://desenv.queromarita.com.br/sacola.html');
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
                var selos = document.getElementById("selos");
                selos.innerHTML = 
                    '<a href="#" onclick="window.open(&apos;https://www.sitelock.com/verify.php?site=queromarita.com.br&apos;,&apos;SiteLock&apos;,&apos;width=600,height=600,left=160,top=170&apos;);" ><img class="img-responsive" alt="SiteLock" title="SiteLock" src="//shield.sitelock.com/shield/queromarita.com.br" /></a><span id="siteseal"></span>';
                var script = document.createElement('script');
                script.type = "text/javascript";
                script.async;
                script.src = "https://seal.godaddy.com/getSeal?sealID=avHfF5eLNR3iPEn1npc84m3Zgd9nD3mdkaHqv0DT80EuLZWzCEsxlKR9ME6s";
                $("head").append(script);
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

function procurar(){
	var uf = document.getElementById("uf").value;
    if(uf.length == 2){
        erroValidacao=0; 
    }else{
        erroValidacao=1;
    }
    
    if(erroValidacao==0){
        var url="http://desenv.queromarita.com.br/bg_localiza_distr.php";
            var parametros = {
                uf: uf,
                fn: "localiza_distr",
            };
            requisicaoHTTP("POST",url,parametros,true,"localiza_distr");
    }else{
        Materialize.toast("Por favor, selecione sua UF.", 4000);
    }
}

function entrar_produto(id){
    window.location.replace('http://desenv.queromarita.com.br/produto_detalhe.html?id='+id);;
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
