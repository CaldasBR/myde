$(document).ready(function(){
    $(".dropdown-button").dropdown();
    $(".button-collapse").sideNav();
    $('.collapsible').collapsible();
    $('select').material_select();
    carregar_rodape();
    exibir_apagar("div_inicio","block");
    $('#email').focus();
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

function carregar_rodape(){
    var url="http://desenv.queromarita.com.br/bg_footer.php";
    var parametros = {
        fn: "le_rodape"
    };
    requisicaoHTTP("GET",url,parametros,true,"le_rodape");
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
                    case "cadastrar_distribuidor":
                        document.getElementById("email_cadastro").value = document.getElementById("email").value;
                        var texto = document.getElementById("texto_cadastro");
                        texto.innerText = jsonData[i].msg;
                        ocultar_apagar("div_inicio");
                        exibir_apagar("div_cadastrar","block");
                        $('#nome_completo').focus();
                        document.getElementById("id_distribuidor").value = jsonData[i].id_distr_cad;
                    break;
                    case "login":
                        document.getElementById("email_login").value = document.getElementById("email").value;
                        ocultar_apagar("div_inicio");
                        ocultar_apagar("div_cadastrar");
                        exibir_apagar("div_login","block");
                        $('#senha_login').focus();
                    break;
                    case "logado":
                        if(jsonData[i].msg == false){
                            exibir_apagar("div_inicio","block");
                            $('#email').focus();
                        }else{
                            ocultar_apagar("div_login");
                            exibir_apagar("div_eh_marita","block");
                        }
                    break;
                    case "pedir_permissao":
                        if(jsonData[i].msg == false){
                            exibir_apagar("div_inicio","block");
                            $('#email').focus();
                        }else{
                            ocultar_apagar("div_login");
                            exibir_apagar("div_inf_autorizacao","block");
                        }
                    break;
                    case "expulsa":
                        window.location.replace('http://desenv.queromarita.com.br/');
                    break;
                    case "distribuidor_aut_mercadopago":
                        ocultar_apagar("div_cadastrar_confirmar");
                        exibir_apagar("div_eh_marita","block");
                    break;
                }
            break;
            case "localiza_distr":
                switch(jsonData[i].opt){
                    case "seleciona_distr":
                        ocultar_apagar("div_cadastrar_cidade");
                        exibir_apagar("div_cadastrar_selecionar","block");
                        document.getElementById('select_distrib_cadastro').innerHTML = jsonData[i].msg;
                    break;
                }
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
            case 'redir_redefacil':
                window.location.href = jsonData[i].msg;
            break;
		}
	}
}

function verifica_email(){
	var email = document.getElementById("email").value;
	var url="http://desenv.queromarita.com.br/bg_login_myde.php";
    var parametros = {
        email: email,
        fn: "cad_distr_verificar",
    };
	requisicaoHTTP("POST",url,parametros,true,"cad_distr_verificar");
}

function login(){
	var email = document.getElementById("email_login").value;
	var senha = md5(document.getElementById("senha_login").value);

	var url="http://desenv.queromarita.com.br/bg_login_myde.php";
    var parametros = {
        email: email,
        senha: senha,
        fn:"logar_aderir",
    };
	requisicaoHTTP("POST",url,parametros,true,"logar_aderir");
}


function autorizar(){
    ocultar_apagar("div_inf_autorizacao");
    ocultar_apagar("div_inf_autorizacao2");
    exibir_apagar("div_vincular","block");
    scroll(0,0);
}

function autorizar_eh_marita(){
    ocultar_apagar("div_eh_marita");
    exibir_apagar("div_inf_autorizacao2","block");
    scroll(0,0);
}

function ir_ufs(){
    ocultar_apagar("div_eh_marita");
    exibir_apagar("div_cadastrar_cidade","block");
    scroll(0,0);
}

function termo_ok(){
    ocultar_apagar("div_termo");
    exibir_apagar("div_inf_autorizacao","block");
    scroll(0,0);
}

function vincular(){
    WindowLogout = window.open("https://www.mercadolibre.com/jms/mlb/lgz/logout", "LogoutMercadoPago", "height=600,width=600");
    setTimeout(function (){
        WindowLogout.close();
        window.location.href = "http://desenv.queromarita.com.br/autorizacao.html";
    },3000);
    
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

function procurar_eh_marita(){
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
                fn: "localiza_distr_eh_marita",
            };
            requisicaoHTTP("POST",url,parametros,true,"localiza_distr_eh_marita");
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
            var url="http://desenv.queromarita.com.br/bg_login_myde.php";
            var parametros = {
                email: email,
                nome: nome_array[0],
                sobrenome: nome_array[1],
                celular: celular,
                cpf: cpf,
                senha: senha,
                id_distr: id_distr,
                fn: "distribuidor_cadastrar",   
            };
            requisicaoHTTP("POST",url,parametros,true,"distribuidor_cadastrar");
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
}

function voltar_cadastro(){
    document.getElementById("senha_cadastro").value = "";
    document.getElementById("confirma_senha").value = "";
    ocultar_apagar("div_cadastrar");
    exibir_apagar("div_inicio","block");
    $('#email').focus();
}

function voltar_autoriza(){
    ocultar_apagar("div_inf_autorizacao");
    exibir_apagar("div_cadastrar_cidade","block");
    scroll(0,0);
}

function voltar_login(){
    ocultar_apagar("div_login");
    exibir_apagar("div_inicio","block");
    $('#email').focus();
    scroll(0,0);
}

function voltar_autoriza(){
    ocultar_apagar("div_inf_autorizacao");
    exibir_apagar("div_termo","block");
    scroll(0,0);
}

function voltar_autoriza2(){
    ocultar_apagar("div_inf_autorizacao2");
    exibir_apagar("div_termo","block");
    scroll(0,0);
}

function voltar_vincular(){
    ocultar_apagar("div_vincular");
    exibir_apagar("div_inf_autorizacao","block");
    scroll(0,0);
}

function voltar_marita(){
    ocultar_apagar("div_eh_marita");
    exibir_apagar("div_inicio","block");
    scroll(0,0);
}

function voltar_cadastro_cidade(){
    ocultar_apagar("div_cadastrar_cidade");
    exibir_apagar("div_inicio","block");
    $('#email').focus();
}

function voltar_cadastro_selecionar(){
    ocultar_apagar("div_cadastrar_selecionar");
    exibir_apagar("div_cadastrar_cidade","block");
    scroll(0,0);
}

function redir_cadastro_mercpag(){
    window.open("https://registration.mercadopago.com.br/registration-mp?mode=mp", "Cadastro MercadoPago", "height=600,width=600");
}

function seleciona_distr(id){
    document.getElementById('id_distr_selecionado').text = id
    ocultar_apagar("div_cadastrar_selecionar");
    exibir_apagar("div_aviso_marita","block");
    scroll(0,0);
}

function redir_marita(){
    var id=document.getElementById('id_distr_selecionado').text;
    var url="http://desenv.queromarita.com.br/bg_redirect_redefacil.php";
    var parametros = {
        id:id,
    };
    requisicaoHTTP("POST",url,parametros,true,"redir_redefacil");
}

function termo(){
    ocultar_apagar("div_eh_marita");
    exibir_apagar("div_termo","block");
    scroll(0,0);
}

function voltar_termo(){
    ocultar_apagar("div_termo");
    exibir_apagar("div_eh_marita","block");
    scroll(0,0);
}


function voltar_aviso_marita(){
    ocultar_apagar("div_aviso_marita");
    exibir_apagar("div_cadastrar_cidade","block");
    scroll(0,0);
}