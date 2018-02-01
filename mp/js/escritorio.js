
$( document ).ready(function() {
	// Initialize collapse button
	$('.button-collapse').sideNav({
		menuWidth: 300, // Default is 300
		edge: 'left', // Choose the horizontal origin
		closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
		draggable: true // Choose whether you can drag to open on touch screens
		}
	);
     $(".dropdown-button").dropdown();
    $('.collapsible').collapsible();
    carregar_menu();
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
            break;
        }
    }
}

// Show sideNav
  $('.button-collapse').sideNav('show');
  // Hide sideNav
  $('.button-collapse').sideNav('hide');
  // Destroy sideNav
  $('.button-collapse').sideNav('destroy');