$(document).ready(function(){
    $('.parallax').parallax();
    $('.slider').slider({height: 400});
    $('.button-collapse').sideNav();
    $('.materialboxed').materialbox();
    carregar_chat();
});

$(document).keypress(function(e) {
    if(e.which == 13) valida_entrar();
});


function valida_entrar(){
    var email = document.getElementById("email_index").value;
    if(email==""){
        window.location = "https://queromarita.com.br/login.html";
    }else{
       window.location = "https://queromarita.com.br/login.html?email="+email;
    }
}


function verificar_sacola(){
    var url="https://queromarita.com.br/bg_sacola__carregar_sacola.php";
    var parametros = {
        fn: "outros"
    };
    requisicaoHTTP("GET",url,parametros,true,"sacola__carregar_sacola");
}

function carregar_chat(){
    var url="https://queromarita.com.br/bg_chat.php";
    var parametros = {
        fn: "carregar_chat"
    };
    requisicaoHTTP("GET",url,parametros,true,"carregar_chat");
}

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
                        window.location.replace('https://queromarita.com.br/sacola.html');
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