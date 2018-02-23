var teste1 = 'oi';
function teste2() {
    var teste3 = 'tchau';

     document.body.innerHTML = 'Dentro da função -  teste1:' + teste1 + '<br>';
     document.body.innerHTML = 'Dentro da função -  teste2:' + teste2 + '<br>';
     document.body.innerHTML = 'Dentro da função -  teste3:' + teste3 + '<br>';
}


 document.body.innerHTML = 'Fora da função -  teste1:' + teste1 + '<br>';
 document.body.innerHTML = 'Fora da função -  teste2:' + teste2 + '<br>';
 document.body.innerHTML = 'Fora da função -  teste3:' + teste3 + '<br>';

 document.body.innerHTML = 'Vou executar -  teste2: <br>';
teste2();
