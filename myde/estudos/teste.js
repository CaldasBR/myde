var teste1 = 'oi';
var teste2 = function(){
    var teste3 = 'tchau';

    console.log('Dentro da função -  teste1:' + teste1);
    console.log('Dentro da função -  teste2:' + teste2);
    console.log('Dentro da função -  teste3:' + teste3);
}


console.log('Fora da função -  teste1:' + teste1);
console.log('Fora da função -  teste2:' + teste2);
console.log('Fora da função -  teste2:' + teste3);
