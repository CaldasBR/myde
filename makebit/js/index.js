$(document).ready(function(){
    $('select').material_select();
    $('.slider').slider({height: 240});
    $('.button-collapse').sideNav();
    $('.parallax').parallax();
});


//Função que trata a resposta do ajax
function trataDados(i){
    var caixa=new Array();
    jsonData[i] = JSON.parse(ajax[i].responseText);
    if(jsonData[i].erro === 1 && jsonData[i].opt === 'Toast'){
        Materialize.toast(jsonData[i].msg, 4000);
    }else{
        switch(jsonData[i].funcao){

        }
    }
}
