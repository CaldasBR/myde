$(document).ready(function(){
    //Popup  modal pagamento
    $(".dropdown-button").dropdown();
    $(".button-collapse").sideNav();
    $('.collapsible').collapsible();
    $('select').material_select();
    carregar_menu();
    carregar_login();
    carregar_chat();
    carregar_rodape();
});

$('#email').keypress(function(e) {
    if(e.which == 13) verifica_email();
});

$('#email_login').keypress(function(e) {
    if(e.which == 13) login();
});

$('#senha_login').keypress(function(e) {
    if(e.which == 13) login();
});

$('#email_cadastro').keypress(function(e) {
    if(e.which == 13) cadastrar();
});

$('#nome_completo').keypress(function(e) {
    if(e.which == 13) cadastrar();
});

$('#celular').keypress(function(e) {
    if(e.which == 13) cadastrar();
});

$('#cpf').keypress(function(e) {
    if(e.which == 13) cadastrar();
});

$('#senha_cadastro').keypress(function(e) {
    if(e.which == 13) cadastrar();
});

$('#confirma_senha').keypress(function(e) {
    if(e.which == 13) cadastrar_confirmar();
});


function carregar_login(){
    var url="https://queromarita.com.br/bg_verif_login.php";
    var parametro_pagina = buscar_parametro('d');
    if(parametro_pagina){
        parametro_pagina = parametro_pagina.replace("%40", "@");
    }else{
        parametro_pagina = '';
    }
    var parametros = {
        fn: "stauts_login",
        d: parametro_pagina
    };
    requisicaoHTTP("GET",url,parametros,true,"stauts_login");
}

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

function carregar_chat(){
    var url="https://queromarita.com.br/bg_chat.php";
    var parametros = {
        fn: "carregar_chat"
    };
    requisicaoHTTP("GET",url,parametros,true,"carregar_chat");
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
                    case "cadastrar_indicado":
                        document.getElementById("email_cadastro").value = document.getElementById("email").value;
                        var texto = document.getElementById("texto_cadastro");
                        texto.innerText = jsonData[i].msg;
                        ocultar_apagar("div_inicio");
                        exibir_apagar("div_cadastrar","block");
                        $('#nome_completo').focus();
                        document.getElementById("id_distribuidor").value = jsonData[i].id_distr_cad;
                        break;
                    case "cadastrar_nao_indicado":
                        ocultar_apagar("div_inicio");
                        exibir_apagar("div_cadastrar_cidade","block");
                        break;
                    case "cadastrar_myde":
                        document.getElementById("email_cadastro").value = document.getElementById("email").value;
                        var texto = document.getElementById("texto_cadastro");
                        texto.innerText = jsonData[i].msg;
                        ocultar_apagar("div_inicio");
                        exibir_apagar("div_cadastrar","block");
                        $('#nome_completo').focus();
                        document.getElementById("id_distribuidor").value = 1;
                        break;
                    case "login":
                        document.getElementById("email_login").value = document.getElementById("email").value;
                        ocultar_apagar("div_inicio");
                        exibir_apagar("div_login","block");
                        document.getElementById('email').value = "";
                        $('#senha_login').focus();
                        break;
                    case "logado":
                        window.location.replace('https://queromarita.com.br/loja.html');
                        break;
                    case "jump_modal":
                        if(jsonData[i].msg == false){
                            exibir_apagar("div_inicio","block");
                            $('#email').focus();
                        }else{
                            window.location.replace('https://queromarita.com.br/loja.html');
                        }
                        break;
                    case "expulsa":
                        window.location.replace('https://queromarita.com.br/');
                        break;
                }
            break;
            case "status_login":
                switch(jsonData[i].opt){
                    case "exibir_login":
                        var val_email = buscar_parametro("email");
                        if(val_email==false){
                            exibir_apagar("div_inicio","block");
                        }else{
                            document.getElementById("email").value = val_email;
                            verifica_email();
                        }

                        if (typeof jsonData[i].msg.id != 'undefined' || jsonData[i].msg.id != null) {
                            document.getElementById("id_distribuidor").value = jsonData[i].msg.id;
                        }
                    break;
                    case "exibir_loja":
                        window.location.replace('https://queromarita.com.br/loja.html');
                    break;
                }
            case "localiza_distr":
                switch(jsonData[i].opt){
                    case "seleciona_distr":
                        ocultar_apagar("div_cadastrar_cidade");
                        exibir_apagar("div_cadastrar_selecionar","block");
                        document.getElementById('select_distrib_cadastro').innerHTML = jsonData[i].msg;
                    break;
                }
            case 'reset_senha':
                switch(jsonData[i].opt){
                    case "Toast":
                        Materialize.toast(jsonData[i].msg, 4000);
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

function verifica_email(){
	var email = document.getElementById("email").value;
	var url="https://queromarita.com.br/bg_login_myde.php";
    var parametros = {
        email: email,
        fn: "verificar",
    };
    var distr = document.getElementById('id_distribuidor').value;
    if(distr != null){
        parametros.distr = distr;
    }
	requisicaoHTTP("POST",url,parametros,true,"verificar");
    run_waitMe('#wait-conteudo', 1, 'win8_linear');
}

function login(){
	var email = document.getElementById("email_login").value;
	var senha = md5(document.getElementById("senha_login").value);

	var url="https://queromarita.com.br/bg_login_myde.php";
    var parametros = {
        email: email,
        senha: senha,
        fn:"logar",
    };
	requisicaoHTTP("POST",url,parametros,true,"logar");
    run_waitMe('#wait-conteudo', 1, 'win8_linear');
}

function cadastrar(){
    erroValidacao = 0;
	var nome_array = valida_nome(document.getElementById("nome_completo").value);
    if(nome_array == false){
        erroValidacao = 1;
    }
    valida_telefone(document.getElementById("celular").value);
    valida_cpf(document.getElementById("cpf").value);
    valida_senha(document.getElementById("senha_cadastro").value);
    if(erroValidacao==0){
        ocultar_apagar("div_cadastrar");
        exibir_apagar("div_cadastrar_confirmar","block");
        $('#confirma_senha').focus();
    }else{
        Materialize.toast("Por favor, verifique os campos do cadastro.", 4000);
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
        var url="https://queromarita.com.br/bg_localiza_distr.php";
            var parametros = {
                uf: uf,
                fn: "localiza_distr",
            };
            requisicaoHTTP("POST",url,parametros,true,"localiza_distr");
            run_waitMe('#wait-conteudo', 1, 'win8_linear');
    }else{
        Materialize.toast("Por favor, selecione sua UF.", 4000);
    }
}

function cadastrar_confirmar(){
    if(erroValidacao==0){
        if(document.getElementById("senha_cadastro").value ==   document.getElementById("confirma_senha").value){
            var nome_array = valida_nome(document.getElementById("nome_completo").value);
            var email = document.getElementById("email_cadastro").value;
            var celular = document.getElementById("celular").value;
            var cpf = document.getElementById("cpf").value;
            var senha = md5(document.getElementById("senha_cadastro").value);
            var id_distr = document.getElementById("id_distribuidor").value;
            var url="https://queromarita.com.br/bg_login_myde.php";
            var parametros = {
                email: email,
                nome: nome_array[0],
                sobrenome: nome_array[1],
                celular: celular,
                cpf: cpf,
                senha: senha,
                id_distr: id_distr,
                fn: "cadastrar",
            };
            requisicaoHTTP("POST",url,parametros,true,"cadastrar");
            run_waitMe('#wait-conteudo', 1, 'win8_linear');
        }else{
            Materialize.toast("As senhas não são as mesmas, verifique.", 4000);
            document.getElementById("confirma_senha").value = "";
        }
    }else{
        Materialize.toast("Por favor, verifique os campos do cadastro.", 4000);
        voltar_cadastro();
    }
}

function voltar_cadastro_confirmar(){
    document.getElementById("senha_cadastro").value = "";
    document.getElementById("confirma_senha").value = "";
    ocultar_apagar("div_cadastrar_confirmar");
    exibir_apagar("div_cadastrar","block");
    $('#nome_completo').focus();
    $("#wait").css("display", "none");
}

function voltar_cadastro(){
    document.getElementById("senha_cadastro").value = "";
    document.getElementById("confirma_senha").value = "";
    ocultar_apagar("div_cadastrar");
    exibir_apagar("div_inicio","block");
    $('#email').focus();
    $("#wait").css("display", "none");
}

function voltar_cadastro_cidade(){
    ocultar_apagar("div_cadastrar_cidade");
    exibir_apagar("div_inicio","block");
    $('#email').focus();
    $("#wait").css("display", "none");
}

function voltar_cadastro_selecionar(){
    ocultar_apagar("div_cadastrar_selecionar");
    ocultar_apagar("div_cadastrar");
    exibir_apagar("div_cadastrar_cidade","block");
    $("#wait").css("display", "none");
}

function voltar_login(){
    document.getElementById("email").value = document.getElementById("email_login").value;
    document.getElementById("email_login").value = "";
    ocultar_apagar("div_login");
    exibir_apagar("div_inicio","block");
    $('#email').focus();
    $("#wait").css("display", "none");
}

function seleciona_distr(id){
    document.getElementById("email_cadastro").value = document.getElementById("email").value;
    document.getElementById("id_distribuidor").value = id;
    ocultar_apagar("div_cadastrar_selecionar");
    exibir_apagar("div_cadastrar","block");
    $('#nome_completo').focus();
}


function existe_email(){
    var url="https://queromarita.com.br/bg_acesso.php";

    var email = document.getElementById('email').value;
    var email_login = document.getElementById('email_login').value;

    if(email == "" & email_login == ""){
        Materialize.toast("Por favor, digite seu email", 4000);
    }else{
        if(email == ""){
            email = email_login;
        }
        var parametros = {
            fn: "existe_email",
            email: email
        };
        requisicaoHTTP("GET",url,parametros,true,"existe_email");
        run_waitMe('#wait-conteudo', 1, 'win8_linear');
    }
}


function exibe_chat(id,usr_nome,usr_email,hash){
    if(usr_nome!="" || usr_email!=""){
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
    }else{
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/'+id+'/default';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    }

}
