var teste1 = 'oi';
var teste2 = function(){
    var teste3 = 'tchau';

    log('Dentro da função -  teste1:' + teste1);
    log('Dentro da função -  teste2:' + teste2);
    log('Dentro da função -  teste3:' + teste3);
}


log('Fora da função -  teste1:' + teste1);
log('Fora da função -  teste2:' + teste2);
log('Fora da função -  teste3:' + teste3);

log('Vou executar -  teste2:');
teste2();
