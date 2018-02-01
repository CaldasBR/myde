var fx_bread = new breadcrumb();
$(document).ready(function(){
    fx_bread.atualizar();
    fx_bread.inserir('cadastro.html','Meus dados');
    $('.modal').modal({dismissible: false});
    $('.collapsible').collapsible();
    $('select').material_select();
    carregar_menu();
    busca_cadastro();
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

//Função que trata a resposta do ajax
function trataDados(i){
	jsonData[i] = JSON.parse(ajax[i].responseText);
	if(jsonData[i].opt === 'Toast'){
		Materialize.toast(jsonData[i].msg, 4000);
        if(jsonData[i].opt2=='refresh'){
            setTimeout(function (){
                window.location.replace('https://queromarita.com.br/cadastro.html');
            },1500);
        }
	}else{
		switch(jsonData[i].funcao){
            case 'verificar_carrinho':
                switch(jsonData[i].opt){
                    case "direcionar_sacola":
                        window.location.replace('https://queromarita.com.br/sacola.html');
                    break;
                }
            break;
            case  "cadastro":
                switch(jsonData[i].opt) {
                    case "le_cadastro":
                        var dados_msg = jsonData[i].msg;
                        document.getElementById("cad_nome_completo").value = dados_msg.nome_completo;
                        document.getElementById("cad_login").innerText = dados_msg.email;   
                        document.getElementById("cad_cep").value = dados_msg.cep;
                        $("#cad_uf").val(dados_msg.uf);
                        document.getElementById("cad_cidade").value = dados_msg.cidade;
                        document.getElementById("cad_bairro").value = dados_msg.bairro;
                        document.getElementById("cad_endereco").value = dados_msg.end_cobranca;
                        document.getElementById("cad_endereco_numero").value = dados_msg.num_imovel;
                        document.getElementById("cad_endereco_compl").value = dados_msg.complemento;
                        document.getElementById("cad_face").value = dados_msg.pg_facebook;
                        document.getElementById("cad_celular").value = dados_msg.cel;
                        document.getElementById("cad_txt_pub").value = dados_msg.txt_apres;
                        document.getElementById("cad_cpf").innerText = dados_msg.cpf;
                        document.getElementById("img_perfil").src = dados_msg.foto;
                        $('select').material_select();
                    break;
                    case "expulsa":
                        window.location.replace('https://queromarita.com.br/');
                    break;
                }
            break;
            case "busca_cep":
                switch(jsonData[i].opt) {
                    case "atualiza_endereco_formulario":
                        $("#cad_uf").val(jsonData[i].msg["uf"]);
                        document.getElementById("cad_cidade").value = jsonData[i].msg["cidade"];
                        document.getElementById("cad_bairro").value = jsonData[i].msg["bairro"];
                        document.getElementById("cad_endereco").value = jsonData[i].msg["logradouro"];
                    break;
                    case "limpa_endereco_formulario":
                        $("#cad_uf").val("");
                        document.getElementById("cad_cidade").value = "";
                        document.getElementById("cad_bairro").value = "";
                        document.getElementById("cad_endereco").value = "";
                    break;
                }
                $('#cad_uf').material_select();
                document.getElementById("cad_endereco_numero").value = "";
                document.getElementById("cad_endereco_compl").value = "";
                $('#cad_endereco_numero').focus();
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

$("#cad_cep").focusout(function(){
    if(document.getElementById("cad_cep").value.length >= 9){
        busca_cep(document.getElementById("cad_cep").value);
    }else{
        Materialize.toast("Por favor, verifique seu CEP.", 4000);
        $("#cad_uf").val("");
        document.getElementById("cad_cidade").value = "";
        document.getElementById("cad_bairro").value = "";
        document.getElementById("cad_endereco").value = "";
        $('#cad_uf').material_select();
        document.getElementById("cad_endereco_numero").value = "";
        document.getElementById("cad_endereco_compl").value = "";
        document.getElementById("cad_cep").value = "";
        $('#cad_cep').focus();
    }
});

function busca_cadastro(){
	var url="https://queromarita.com.br/bg_cadastro.php";
    var parametros = {
        fn: "le_cadastro",
    };
	requisicaoHTTP("GET",url,parametros,true,"le_cadastro");
}

function busca_cep(cep){
	var url="https://queromarita.com.br/bg_busca_cep.php";
    var parametros = {
        fn: "busca_cep",
        cep: cep,
    };
	requisicaoHTTP("GET",url,parametros,true,"busca_dados_cli");
}

function atualiza_cadastro(){
    var url="https://queromarita.com.br/bg_cadastro.php";
    var nome_array = valida_nome(document.getElementById("cad_nome_completo").value);
    
    var parametros = {
        fn: "salva_cadastro",
        
        nome: nome_array[0],
        sobrenome: nome_array[1],
        nome_completo: nome_array[0] + ' ' + nome_array[1],
        cep: apenasNumeros(document.getElementById("cad_cep").value),
        uf: document.getElementById("cad_uf").value,
        cidade: document.getElementById("cad_cidade").value,
        bairro: document.getElementById("cad_bairro").value,
        end_cobranca: document.getElementById("cad_endereco").value,
        num_imovel: document.getElementById("cad_endereco_numero").value,
        complemento: document.getElementById("cad_endereco_compl").value,
        pais: "Brasil",
        pg_facebook: document.getElementById("cad_face").value,
        cel: document.getElementById("cad_celular").value,
        txt_apres: document.getElementById("cad_txt_pub").value,
    };
    requisicaoHTTP("POST",url,parametros,true,"salva_cadastros");   
}

function atualiza_senha(){
    var url="https://queromarita.com.br/bg_cadastro.php";
    
    if(md5(document.getElementById("senha_nova").value) == md5(document.getElementById("senha_confirm").value)){
        var parametros = {
            fn: "salva_senha",

            atual: md5(document.getElementById("senha_atual").value),
            nova: md5(document.getElementById("senha_nova").value),
        };
        requisicaoHTTP("POST",url,parametros,true,"salva_senha");       
    }else{
        Materialize.toast("A senha nova não é igual a confimação, por favor verifique", 4000);
        document.getElementById("senha_nova").value = "";
        document.getElementById("senha_confirm").value = "";
        $('#senha_nova').focus();
    }
    
}

//Start Validação CPF
$("#cad_cep").focusout(function(){
    if(document.getElementById("cad_cep").value.length >= 9){
        busca_cep(document.getElementById("cad_cep").value);
    }else{
        Materialize.toast("Por favor, verifique seu CEP.", 4000);
        $("#cad_uf").val("");
        document.getElementById("cad_cidade").value = "";
        document.getElementById("cad_bairro").value = "";
        document.getElementById("cad_endereco").value = "";
        $('#cad_uf').material_select();
        document.getElementById("cad_endereco_numero").value = "";
        document.getElementById("cad_endereco_compl").value = "";
        document.getElementById("cad_cep").value = "";
        $('#cad_cep').focus();
    }
});

function busca_cep(cep){
	var url="https://queromarita.com.br/bg_busca_cep.php";
    var parametros = {
        fn: "busca_cep",
        cep: cep,
    };
	requisicaoHTTP("GET",url,parametros,true,"busca_dados_cli");
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
