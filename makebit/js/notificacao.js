var controlaPopup=0;

//## Script que gera notificação na tela ## 
function notificacao(elemento,texto,funcao){
	if (controlaPopup!==1){
		var txt_popup = document.getElementById("txtPopWarning");	
		txt_popup.innerHTML = texto;
	
		var conteudoPopup = document.getElementById("divConteudoPop");
		while (conteudoPopup.firstChild){
			conteudoPopup.removeChild(conteudoPopup.firstChild);
		}
	
		if(funcao==="login_myde"){
			adicionaSenhaFrmPop();
		}
		
		if(funcao==="login_facebook"){
			adicionaFbPopup();
		}
		
		if(funcao==="cadastrar"){
			adicionaCadastroPopup();
		}
	
		$("#popWarning").openModal({
			complete: function () {
	      	controlaPopup = 0;
   		 }
		});
		controlaPopup = 1;		
	}
}

//Função que coloca os campos de login e senha na popup de warning
function adicionaSenhaFrmPop(){
	var popup = document.getElementById("popWarning");
	var frmPasswd = document.createElement("form");
	frmPasswd.setAttribute("id","frmLoginPop");
	frmPasswd.setAttribute("method","post");	
	var txtPasswd = document.createElement("input");
	txtPasswd.setAttribute("data-role","fieldcontain");
	txtPasswd.setAttribute("name","txtPasswdPop");
	txtPasswd.setAttribute("id","txtPasswdPop");
	txtPasswd.setAttribute("required","true");
	txtPasswd.setAttribute("placeholder","Senha");
	txtPasswd.setAttribute("type","password");
	txtPasswd.setAttribute("class","center-align");
	txtPasswd.setAttribute("style","max-width:90%;margin-left:5%;");
	frmPasswd.appendChild(txtPasswd);
	
	var divButtons = document.createElement("div");
	divButtons.setAttribute("data-role","controlgroup");
	divButtons.setAttribute("data-type","horizontal");
		
	var funcao1_popup = document.createElement("a");
	funcao1_popup.setAttribute("href","javascript:void(0);");
	funcao1_popup.setAttribute("onClick","javascript:login(1);");
	funcao1_popup.setAttribute("class","waves-effect waves-light btn light-blue darken-4");
	funcao1_popup.setAttribute("style","margin:10px;");
	funcao1_popup.innerHTML = "Login";	
	divButtons.appendChild(funcao1_popup);
	
	var funcao2_popup = document.createElement("a");
	funcao2_popup.setAttribute("href","javascript:void(0);");
	funcao2_popup.setAttribute("onClick","javascript:fecha_mod_warning();");
	funcao2_popup.setAttribute("class","waves-effect waves-light btn light-blue darken-4");
	funcao2_popup.setAttribute("style","margin:10px;");
	funcao2_popup.innerHTML = "Corrigir Email";
	divButtons.appendChild(funcao2_popup);
	
	frmPasswd.appendChild(divButtons);
	var conteudoPopup = document.getElementById("divConteudoPop");
	conteudoPopup.appendChild(frmPasswd);
	popup.appendChild(conteudoPopup);
}


function adicionaFbPopup(){
	var popup = document.getElementById("popWarning");
	var divButtons = document.createElement("div");
	divButtons.setAttribute("data-role","controlgroup");
	divButtons.setAttribute("data-type","horizontal");
	var funcao1_popup = document.createElement("a");
	funcao1_popup.setAttribute("href","javascript:void(0);");
	funcao1_popup.setAttribute("onClick","FBLogin();");
	funcao1_popup.setAttribute("class","waves-effect waves-light btn light-blue darken-4");
	funcao1_popup.setAttribute("style","margin:10px;");
	funcao1_popup.innerHTML = "Login pelo Facebook";
	divButtons.appendChild(funcao1_popup);
	
	var funcao2_popup = document.createElement("a");
	funcao2_popup.setAttribute("href","javascript:void(0);");
	funcao2_popup.setAttribute("onClick","javascript:fecha_mod_warning();");
	funcao2_popup.setAttribute("class","waves-effect waves-light btn light-blue darken-4");
	funcao2_popup.setAttribute("style","margin:10px;");
	funcao2_popup.innerHTML = "Corrigir Email";
	divButtons.appendChild(funcao2_popup);
	
	var conteudoPopup = document.getElementById("divConteudoPop");
	conteudoPopup.appendChild(divButtons);
	popup.appendChild(conteudoPopup);
}

function adicionaCadastroPopup(){
	var popup = document.getElementById("popWarning");
	var divButtons = document.createElement("div");
	divButtons.setAttribute("data-role","controlgroup");
	divButtons.setAttribute("data-type","horizontal");
	var funcao1_popup = document.createElement("a");
	funcao1_popup.setAttribute("href","acesso_cadastro.html");
	funcao1_popup.setAttribute("class","waves-effect waves-light btn light-blue darken-4");
	funcao1_popup.setAttribute("style","margin:10px;");
	funcao1_popup.innerHTML = "Sim";
	divButtons.appendChild(funcao1_popup);
	
	var funcao2_popup = document.createElement("a");
	funcao2_popup.setAttribute("href","javascript:void(0);");
	funcao2_popup.setAttribute("onClick","javascript:fecha_mod_warning();");
	funcao2_popup.setAttribute("class","waves-effect waves-light btn light-blue darken-4");
	funcao2_popup.setAttribute("style","margin:10px;");
	funcao2_popup.innerHTML = "Não";
	divButtons.appendChild(funcao2_popup);
	
	var conteudoPopup = document.getElementById("divConteudoPop");
	conteudoPopup.appendChild(divButtons);
	popup.appendChild(conteudoPopup);
}

function fecha_mod_senha(){
	$('#popCheckSenha').closeModal();
	controlaPopup = 0;
	erroValidacao = 0;
}

function fecha_mod_warning(){
	$('#popWarning').closeModal();
	controlaPopup = 0;
}