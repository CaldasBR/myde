$(document).ready(function(){
    //Popup  modal pagamento
    $('.modal').modal();
    $('select').material_select();
    //carregar_loja();
    $('#modal_cadastrar_cidade').modal('open');
    $('.slider').slider({height: 240});
    $('.button-collapse').sideNav();
});

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
                        $('#modal_inicio').modal('close');
                        document.getElementById("email_cadastro").value = document.getElementById("email").value;
                        var texto = document.getElementById("texto_cadastro");
                        texto.innerText = jsonData[i].msg;
                        $('#modal_cadastrar').modal('open');
                        $('#nome_completo').focus();
                        document.getElementById("id_distribuidor").value = jsonData[i].id_distr_cad;
                        break;
                    case "cadastrar_myde":
                        $('#modal_inicio').modal('close');
                        document.getElementById("email_cadastro").value = document.getElementById("email").value;
                        var texto = document.getElementById("texto_cadastro");
                        texto.innerText = jsonData[i].msg;
                        $('#modal_cadastrar').modal('open');
                        $('#nome_completo').focus();
                        document.getElementById("id_distribuidor").value = 1;
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
                        carregar_loja();
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
                        window.location.replace('https://queromarita.com.br/');
                        break;
                }
            break;
            case "loja":
                switch(jsonData[i].opt){
                    case "loja__carregar_loja":
                        caixa[i].innerHTML=jsonData[i].code;
                    break;
                    case "popup":
                        $('#modal_inicio').modal('open');
                    break;
                }
            break;
            case "localiza_distr":
                switch(jsonData[i].opt){
                    case "seleciona_distr":
                        $('#modal_cadastrar_selecionar').modal('open');
                        document.getElementById('select_distrib_cadastro').innerHTML = jsonData[i].msg;
                    break;
                }
		}
        if(jsonData[i].funcao === "loja"){
            switch(jsonData[i].opt) {
                case "loja__carregar_loja":
                    caixa[i].innerHTML=jsonData[i].code;
                    break;    
            }
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
	requisicaoHTTP("POST",url,parametros,true,"verificar");
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

function voltar_cadastro_cidade(){
    document.getElementById("senha_cadastro").value = "";
    document.getElementById("confirma_senha").value = "";
    $('#modal_cadastrar_cidade').modal('close');
    $('#modal_inicio').modal('open');
    $('#email').focus();
}

function voltar_login(){
    $('#modal_login').modal('close');
    $('#modal_inicio').modal('open');
    $('#email').focus();
}

function carregar_loja(){
    var url="https://queromarita.com.br/bg_loja__carregar_loja.php";
    var parametros = {
        1:1
    };
    requisicaoHTTP("GET",url,parametros,true,"loja__carregar_loja");
}

function entrar_produto(id){
    window.location.replace('https://queromarita.com.br/produto_detalhe.html?id='+id);;
}
