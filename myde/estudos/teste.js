var teste1 = 'oi';
function teste2() {
    var teste3 = 'tchau';

    console.log('Dentro da função -  teste1:' + teste1);
    console.log('Dentro da função -  teste2:' + teste2);
    console.log('Dentro da função -  teste3:' + teste3);
}


console.log('Fora da função -  teste1:' + teste1);
console.log('Fora da função -  teste2:' + teste2);
console.log('Fora da função -  teste3:' + teste3);

console.log('Vou executar -  teste2:');
teste2();
