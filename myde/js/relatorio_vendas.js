$(document).ready(function(){
    $('select').material_select();    
    $('.slider').slider({height: 240});
    $('.button-collapse').sideNav();
    carregar_menu();
    carregar_graficos('graf_vendas_qtde_dia');
    carregar_graficos('graf_vendas_valor_dia');
    carregar_graficos('graf_vendas_qtde_mes');
    carregar_graficos('graf_vendas_valor_mes');
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
    var caixa=new Array();
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
            case "carrega_grafs":
                switch(jsonData[i].opt){
                    case "graf_vendas_qtde_dia":
                        drawChart('graf_1',jsonData[i].msg,'ColumnChart','Quantidade vendida por dia',false);
                    break;
                    case "graf_vendas_valor_dia":
                        drawChart('graf_2',jsonData[i].msg,'ColumnChart','R$ recebido por dia',false);
                    break;
                    case "graf_vendas_qtde_mes":
                        drawChart('graf_3',jsonData[i].msg,'ColumnChart','Quantidade vendida por mês',false);
                    break;
                    case "graf_vendas_valor_mes":
                        drawChart('graf_4',jsonData[i].msg,'ColumnChart','R$ recebido por mês',false);
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

function carregar_graficos(graf){
    var url="https://queromarita.com.br/bg_relatorio_vendas.php";
    var parametros = {
        fn: graf,
    };
    requisicaoHTTP("GET",url,parametros,true,graf);
}

function drawChart(container,dados,type,title,stacked){
    //console.log(dados);
    
    var wrapper = new google.visualization.ChartWrapper({
        chartType: type,
        dataTable: dados,
        options: {
            'title': title
        },
        containerId: container
    });
    wrapper.draw();
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
