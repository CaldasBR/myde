var fx_bread = new breadcrumb();
$(document).ready(function (){
    fx_bread.atualizar();
    fx_bread.inserir('sacola.html','Carrinho');
    $('.slider').slider({height: 360});
    $('.button-collapse').sideNav();    
    $('select').material_select();
    $('.modal').modal();
    carregar_menu();
    carregar_sacola();
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

//Máscara ao digitar CPF
$("#cpf").keyup(function() {
    document.getElementById("cpf").value = cpf_mask(document.getElementById("cpf").value);
});

//Máscara Celular
$("#celular").keyup(function() {
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
            case 'verificar_carrinho':
                switch(jsonData[i].opt){
                    case "direcionar_sacola":
                        window.location.replace('http://desenv.queromarita.com.br/sacola.html');
                    break;
                }
            break;
            case "login_myde":
            switch(jsonData[i].opt) {
                case "cadastrar_indicado":
                    $('#modal_inicio').modal('close');
                    document.getElementById("email_cadastro").value = document.getElementById("email").value;
                    var texto = document.getElementById("texto_cadastro");
                    texto.innerText = jsonData[i].msg;
                    $('#modal_cadastrar').modal('open');
                    $('#nome_completo').focus();
                    break;
                case "cadastrar_myde":
                    $('#modal_inicio').modal('close');
                    document.getElementById("email_cadastro").value = document.getElementById("email").value;
                    var texto = document.getElementById("texto_cadastro");
                    texto.innerText = jsonData[i].msg;
                    $('#modal_cadastrar').modal('open');
                    $('#nome_completo').focus();
                    break;
                case "login":
                    document.getElementById("email_login").value = document.getElementById("email").value;
                    $('#modal_cadastrar').modal('close');
                    $('#modal_login').modal('open');
                    $('#senha_login').focus();
                    break;
                case "logado":
                    $('#modal_inicio').modal('close');
                    $('#modal_cadastrar').modal('close');
                    $('#modal_cadastrar_confirmar').modal('close');
                    $('#modal_login').modal('close');
                    break;
                case "jump_modal":
                    if(jsonData[i].msg == false){
                        $('.modal').modal({dismissible: false});
                        $('#modal_inicio').modal('open');
                        $('#email').focus();
                    }else{
                        $('#modal_inicio').modal('close');
                        $('#modal_cadastrar').modal('close');
                        $('#modal_cadastrar_confirmar').modal('close');
                        $('#modal_login').modal('close');
                    }
                    break;
                case "expulsa":
                    window.location.replace('http://desenv.queromarita.com.br/');
                    break;
            } 
            break;
            case "sacola":
                switch(jsonData[i].opt) {
                    case "sacola__carregar_sacola":
                        caixa[i].innerHTML=jsonData[i].msg;
                        $('select').material_select();
                        atualizar_valor_inicio();
                    break;
                    /*case "sacola__calcular_frete":
                        var elem_valor_frete=document.getElementById("valor_frete");
                        var valor_frete=valor_produto=jsonData[i].msg.toFixed(2).replace(".", ",");
                        var t = document.createTextNode(valor_frete);     // Create a text node
                        while (elem_valor_frete.firstChild) {
                            elem_valor_frete.removeChild(elem_valor_frete.firstChild);
                        }
                        elem_valor_frete.appendChild(t);                         
                    break;*/
                    case "sacola__comprar":
                        window.location.replace('http://desenv.queromarita.com.br/bg_pagamento_transp.php?fn=pagar_distrib');
                    break;
                    case "sacola__remover":
                        //window.location.replace('http://desenv.queromarita.com.br/sacola.html');
                        carregar_sacola();
                        atualizar_valor_inicio();
                    break;
                    case "sacola__carregar_sacola__sacola_vazia":
                        window.location.replace('http://desenv.queromarita.com.br/loja.html');
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

function verifica_email(){
	var email = document.getElementById("email").value;
	var url="http://desenv.queromarita.com.br/bg_login_myde.php";
    var parametros = {
        email: email,
        fn: "verificar",
    };
	requisicaoHTTP("POST",url,parametros,true,"verificar");
}

function login(){
	var email = document.getElementById("email_login").value;
	var senha = md5(document.getElementById("senha_login").value);

	var url="http://desenv.queromarita.com.br/bg_login_myde.php";
    var parametros = {
        email: email,
        senha: senha,
        fn:"logar"
    };
	requisicaoHTTP("POST",url,parametros,true,"logar");
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
        $('#modal_cadastrar').modal('close');
        $('#modal_cadastrar_confirmar').modal('open');
        $('#confirma_senha').focus();
    }else{
        Materialize.toast("Por favor, verifique os campos do cadastro.", 4000);
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
            var url="http://desenv.queromarita.com.br/bg_login_myde.php";
            var parametros = {
                email: email,
                nome: nome_array[0],
                sobrenome: nome_array[1],
                celular: celular,
                cpf: cpf,
                senha: senha,
                fn: "cadastrar",   
            };
            requisicaoHTTP("POST",url,parametros,true,"cadastrar");
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
    $('#modal_cadastrar_confirmar').modal('close');
    $('#modal_cadastrar').modal('open');
    $('#nome_completo').focus();
}

function voltar_cadastro(){
    document.getElementById("senha_cadastro").value = "";
    document.getElementById("confirma_senha").value = "";
    $('#modal_cadastrar').modal('close');
    $('#modal_inicio').modal('open');
    $('#email').focus();
}

function voltar_login(){
    $('#modal_login').modal('close');
    $('#modal_inicio').modal('open');
    $('#email').focus();
}

function verifica_session(){
	var url="http://desenv.queromarita.com.br/bg_login_myde.php";
    var parametros = {
        fn: "session",
    };
	requisicaoHTTP("POST",url,parametros,true,"verif_session");
}

function carregar_sacola(){
    var url="http://desenv.queromarita.com.br/bg_sacola__carregar_sacola.php";
    var parametros = {
        fn: "sacola"
    };
    requisicaoHTTP("GET",url,parametros,true,"sacola__carregar_sacola");
}

function entrar_produto(id){
    window.location.replace('http://desenv.queromarita.com.br/produto_detalhe.html?id='+id);
}
//Popup  modal pagamento
 
function atualizar_qtde(id, valor_unit){
    alterar_qtde(id, valor_unit);
    calcular_subtotal_produtos();
    //calcular_frete();
    //calcular_valor_total();
}
function atualizar_valor_inicio(){
    calcular_subtotal_produtos();
    //calcular_frete();
    //calcular_valor_total();
}
/*function atualizar_frete(){
    calcular_frete();
    calcular_valor_total();
}*/
function alterar_qtde(id, valor_unit){
    var qtde=document.getElementById("qtde_"+id).value;
    var valor_atual=document.getElementById("valor_total_"+id);
    var valor_novo=qtde*valor_unit;
     valor_novo=valor_novo.toFixed(2).replace(".", ",");
    var t = document.createTextNode("R$ "+valor_novo);     // Create a text node
    while (valor_atual.firstChild) {
        valor_atual.removeChild(valor_atual.firstChild);
    }
    valor_atual.appendChild(t); 
    var url="http://desenv.queromarita.com.br/bg_sacola__atualizar_qtde.php";
    var parametros = {qtde:qtde,
                      id:id
    };
    requisicaoHTTP("GET",url,parametros,true,"sacola__atualizar_qtde");
}


function calcular_subtotal_produtos(){
    var campos = document.body.getElementsByTagName("span");
    var parametros = {};
    var valor_produto = 0;
    for (var i = campos.length - 1; i >= 0; i--) {
        var campo = campos[i];
        if(campo.id.substring(0,11) == "valor_total"){
            valor_produto = (valor_produto + parseFloat(campo.innerText.substring(3,21).replace(",",".")));
        }
    }
    var elem_valor_produto=document.getElementById("valor_final");
    valor_produto=valor_produto.toFixed(2).replace(".", ",");
    var t = document.createTextNode(valor_produto);     // Create a text node
    while (elem_valor_produto.firstChild) {
    elem_valor_produto.removeChild(elem_valor_produto.firstChild);
    }
    elem_valor_produto.appendChild(t); 
}


/*function calcular_frete(){
    var campos= document.body.getElementsByTagName("select");
    var parametros = {};    
    for (var i = campos.length - 1; i >= 0; i--) {
        var campo = campos[i];
        parametros[campo.id] = campo.value; 
//        alert(campo.id.substring(5,99999999999)+"="+campo.value);
    }
    parametros['cep'] = apenasNumeros(document.getElementById("cep").value);
    parametros['fn']='sacola';
    var url="http://desenv.queromarita.com.br/bg_consulta_frete.php";

    requisicaoHTTP("GET",url,parametros,true,"sacola__consultar_frete");
} 

function calcular_valor_total(){
    //var elem_valor_produto=document.getElementById("valor_produto");
    //var elem_valor_frete=document.getElementById("valor_frete");
    var elem_valor_total=document.getElementById("valor_produto");
    //var valor_produto=document.getElementById("valor_produto").value;
    //var valor_frete=document.getElementById("valor_frete").value;
    valor_produto=parseFloat(elem_valor_total.innerText.replace(",","."));
    //valor_frete=parseFloat(elem_valor_frete.innerText.replace(",","."));
    var valor_total=valor_produto;
    valor_total=valor_total.toFixed(2).replace(".", ",");
    var t = document.createTextNode(valor_total);     // Create a text node
    while (elem_valor_total.firstChild) {
        elem_valor_total.removeChild(elem_valor_total.firstChild);
    }
    elem_valor_total.appendChild(t);     
}
*/

function remover_produto(id){
    var url="http://desenv.queromarita.com.br/bg_sacola__remover.php";
    var parametros = {
        id:id,
        fn:"sacola"
        
    };
    requisicaoHTTP("GET",url,parametros,true,"sacola__remover");
} 
function comprar(){    
    var url="http://desenv.queromarita.com.br/bg_sacola__comprar.php";
    var parametros = {
        1:1
    };
    requisicaoHTTP("GET",url,parametros,true,"sacola__comprar");        
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
