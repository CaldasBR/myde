var result ='';

var teste1 = 'oi';
function teste2() {
    var teste3 = 'tchau';

     result += 'Dentro da função -  teste1:' + teste1 + '<br>';
     result += 'Dentro da função -  teste2:' + teste2 + '<br>';
     result += 'Dentro da função -  teste3:' + teste3 + '<br>';
}


result += 'Fora da função -  teste1:' + teste1 + '<br>';
result += 'Fora da função -  teste2:' + teste2 + '<br>';

result += 'Vou executar -  teste2: <br>';
teste2();

document.body.innerHTML = result;
