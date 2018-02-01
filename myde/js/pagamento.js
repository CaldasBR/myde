$(document).ready(function() {
    $('.modal').modal({dismissible: false});
    $('select').material_select();
    $("#cob_email").keyup(function(){
        $("#email").val($("#cob_email").val());
    });
    $(".dropdown-button").dropdown();
    $(".button-collapse").sideNav();
    $('.collapsible').collapsible();
    carregar_menu();
    busca_dados_cli();
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

Mercadopago.setPublishableKey("APP_USR-fa81f29c-e1d7-417c-8e04-c33253192f38");
//Mercadopago.getIdentificationTypes();

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
                        window.location.replace('https://queromarita.com.br/sacola.html');
                    break;
                }
            break;
            case  "pagamento":
                switch(jsonData[i].opt) {
                    case "resgata_cliente":
                        var dados_msg = jsonData[i].msg;
                            document.getElementById("cob_email").value = dados_msg.email;
                            document.getElementById("email").value = dados_msg.email;
                            document.getElementById("cob_nome").value = dados_msg.nome;
                            document.getElementById("cob_sobrenome").value = dados_msg.sobrenome;
                            document.getElementById("cob_telefone").value = dados_msg.cel;
                    break;
                    case "criar_customer":
                        salva_customer();
                    break;
                    case "expulsa":
                        window.location.replace('https://queromarita.com.br/');
                    break;
                }
            break;    
            case  "assinatura":
                switch(jsonData[i].opt){
                    case "expulsa":
                        window.location.replace('https://queromarita.com.br/');
                    break;
                    case 'redirect_escritorio':
                        //$('#modal_parabens').modal('open');
                        window.location.replace('https://queromarita.com.br/estoque.html');
                    break;
                }
            break;
            case "busca_cep":
                switch(jsonData[i].opt) {
                    case "atualiza_endereco_formulario":
                        $("#cob_uf").val(jsonData[i].msg["uf"]);
                        document.getElementById("cob_cidade").value = jsonData[i].msg["cidade"];
                        document.getElementById("cob_bairro").value = jsonData[i].msg["bairro"];
                        document.getElementById("cob_endereco").value = jsonData[i].msg["logradouro"];
                    break;
                    case "limpa_endereco_formulario":
                        $("#cob_uf").val("");
                        document.getElementById("cob_cidade").value = "";
                        document.getElementById("cob_bairro").value = "";
                        document.getElementById("cob_endereco").value = "";
                    break;
                }
                $('#cob_uf').material_select();
                document.getElementById("cob_endereco_numero").value = "";
                document.getElementById("cob_endereco_compl").value = "";
                $('#cob_endereco_numero').focus();
            break;
            case "customer":
                switch(jsonData[i].opt) {
                    case "efetuar_pagamento":
                        contratar_assinatura($('#plano').val());
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
        }
    }
}

function busca_dados_cli(){
	var url="https://queromarita.com.br/bg_pagamento.php";
    var parametros = {
        fn: "resgata_cliente",
    };
	requisicaoHTTP("GET",url,parametros,true,"busca_dados_cli");
}

//Start Validação CPF
$("#cob_cep").focusout(function(){
    if(document.getElementById("cob_cep").value.length >= 9){
        busca_cep(document.getElementById("cob_cep").value);
    }else{
        Materialize.toast("Por favor, verifique seu CEP.", 4000);
        $("#cob_uf").val("");
        document.getElementById("cob_cidade").value = "";
        document.getElementById("cob_bairro").value = "";
        document.getElementById("cob_endereco").value = "";
        $('#cob_uf').material_select();
        document.getElementById("cob_endereco_numero").value = "";
        document.getElementById("cob_endereco_compl").value = "";
        document.getElementById("cob_cep").value = "";
        $('#cob_cep').focus();
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

function addEvent(el, eventName, handler){
	if (el.addEventListener) {
		   el.addEventListener(eventName, handler);
	} else {
		el.attachEvent('on' + eventName, function(){
		  handler.call(el);
		});
	}
};

function getBin() {
	var ccNumber = document.querySelector('input[data-checkout="cardNumber"]');
	return ccNumber.value.replace(/[ .-]/g, '').slice(0, 6);
};

function guessingPaymentMethod(event) {
	var bin = getBin();

	if (event.type == "keyup") {
		if (bin.length >= 6) {
			Mercadopago.getPaymentMethod({
				"bin": bin
			}, setPaymentMethodInfo);
		}
		else{
			$("#paymentIMG").attr("src", "");
		}
	} else {
		setTimeout(function() {
			if (bin.length >= 6) {
				Mercadopago.getPaymentMethod({
					"bin": bin
				}, setPaymentMethodInfo);
			}
		}, 100);
	}
};

function setPaymentMethodInfo(status, response) {
	if (status == 200) {
		// do somethings ex: show logo of the payment method
		var form = document.querySelector('#pay');

		if (document.querySelector("input[name=paymentMethodId]") == null) {
			var paymentMethod = document.createElement('input');
			paymentMethod.setAttribute('name', "paymentMethodId");
			paymentMethod.setAttribute('type', "hidden");
			paymentMethod.setAttribute('value', response[0].id);
			form.appendChild(paymentMethod);
		} else {
			document.querySelector("input[name=paymentMethodId]").value = response[0].id;
		}
		
		if (document.querySelector("img[name=paymentIMG]") != null) {
			$("#paymentIMG").attr("src", "media/imagens/"+response[0].id+".jpg");
		}
	}
};

addEvent(document.querySelector('input[data-checkout="cardNumber"]'), 'keyup', guessingPaymentMethod);
addEvent(document.querySelector('input[data-checkout="cardNumber"]'), 'change', guessingPaymentMethod);


function doPay(){
    if($('#plano').val()!=""){
        document.getElementById("cardExpirationMonth").value = $( "#ExpirationMonthSelect" ).val();
        document.getElementById("cardExpirationYear").value = $( "#ExpirationYearSelect" ).val();
        var $form = document.querySelector('#pay');
        Mercadopago.createToken($form, sdkResponseHandler); // The function "sdkResponseHandler" is defined below
    }else{
        Materialize.toast("Por favor, selecione um plano.", 4000);
    }
}

function sdkResponseHandler(status, response){
    if(status != 200 && status != 201){
        for(var erroCart in response.cause) {
            //alert(response.cause[erroCart].code);
            switch(response.cause[erroCart].code){
                case '205':
                    Materialize.toast("Digite o seu número de cartão.", 4000);
                    break;
                case '208':
                    Materialize.toast("Escolha um mês de validade do cartão.", 4000);
                    break;
                case '209':
                    Materialize.toast("Escolha um ano de validade do cartão.", 4000);
                    break;
                case '212':
                    Materialize.toast("Insira o número do seu CPF.", 4000);
                    break;
                case '213':
                    Materialize.toast("Informe o tipo do seu documento.", 4000);
                    break;
                case '214':
                    Materialize.toast("Insira o número do seu CPF.", 4000);
                    break
                case '221':
                    Materialize.toast("Insira o nome e o sobrenome.", 4000);
                    break;
                case '224':
                    Materialize.toast("Digite o código de segurança.", 4000);
                    break;
                case 'E301':
                    Materialize.toast("Há algo errado com este número de cartão. Volte a digitá-lo.", 4000);
                    break;
                case 'E302':
                    Materialize.toast("Revise o código de segurança.", 4000);
                    break;
                case '316':
                    Materialize.toast("Insira um nome válido.", 4000);
                    break;
                case '322':
                    Materialize.toast("Revise o seu tipo do seu documento.", 4000);
                    break;
                case '323':
                    Materialize.toast("Revise o seu tipo do seu documento.", 4000);
                    break;
                case '324':
                    Materialize.toast("Revise o número do seu CPF.", 4000);
                    break;
                case '325':
                    Materialize.toast("Revise a data de validade do cartão.", 4000);
                    break;
                case '326':
                    Materialize.toast("Revise a data de validade do cartão.", 4000);
                    break;
                default:
                    Materialize.toast("Revise os dados do cartão e de identificação.", 4000);
            }
        }
    }else{
        salva_cartao(response);
    }
}

function salva_customer(){
    var url="https://queromarita.com.br/bg_customers.php";
    var parametros = {
        fn: "salva_customer",
        email: document.getElementById("cob_email").value,
        first_name: document.getElementById("cob_nome").value,
        last_name:  document.getElementById("cob_sobrenome").value,
        phone: document.getElementById("cob_telefone").value,
        cpf: document.getElementById("docNumber").value,
        zip_code: document.getElementById("cob_cep").value,
        street_name: document.getElementById("cob_endereco").value,
        street_number: document.getElementById("cob_endereco_numero").value,
        street_compl: document.getElementById("cob_endereco_compl").value,
        pais: $( "#cob_country" ).val(), 
        uf: document.getElementById("cob_uf").value,
        cidade: document.getElementById("cob_cidade").value,
        bairro: document.getElementById("cob_bairro").value,
    };
    requisicaoHTTP("POST",url,parametros,true,"salva_customer");   
}

function salva_cartao(response){
    var url="https://queromarita.com.br/bg_pagamento.php";
    var parametros = {
        fn: "salva_cartao",
        card_id: response.card_id,
        card_number_length: response.card_number_length,
        cardholder_name: response.cardholder.name,
        cardholder_ident_type: response.cardholder.identification.type,
        cardholder_ident_number: response.cardholder.identification.number,
        date_created: response.date_created,
        date_due: response.date_due,
        date_last_updated: response.date_last_updated,
        date_used: response.date_used,
        expiration_month: response.expiration_month,
        expiration_year: response.expiration_year,
        first_six_digits: response.first_six_digits,
        id: response.id,
        last_four_digits: response.last_four_digits,
        live_mode: response.live_mode,
        luhn_validation: response.luhn_validation,
        public_key: response.public_key,
        security_code_length: response.security_code_length,
        status: response.status,
        method: document.getElementById("paymentMethodId").value,
    };
    requisicaoHTTP("POST",url,parametros,true,"salva_cartao");   
}

function contratar_assinatura(id_assinatura){
    var url="https://queromarita.com.br/bg_assinatura.php";
    var parametros = {
        fn: "contratar_assinatura",
        id_assinatura: id_assinatura,
    };
    requisicaoHTTP("POST",url,parametros,true,"contratar_assinatura");   
}

function configurar(){
    window.location.replace('https://queromarita.com.br/estoque.html');
}