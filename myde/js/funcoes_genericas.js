    var erroValidacao = 1;
//##########################################################################################################
//										Script para todas as funções genéricas
//										Última modificação: 17/04/2017 - Filipe Caldas
//##########################################################################################################


//##########################################################################################################
//												Ocultar e Exibir itens
//##########################################################################################################
function ocultar_transparente(nome){
	document.getElementById(nome).style.visibility = "hidden";
}

function ocultar_apagar(nome){
	document.getElementById(nome).style.display = "none";
}

function exibir_transparente(nome){
	document.getElementById(nome).style.visibility = "visible";
}

function exibir_apagar(nome,tp_display){
/*
tp_display				Description
block = 				Element is rendered as a block-level element
compact = 				Element is rendered as a block-level or inline element. Depends on context
flex = 					Element is rendered as a block-level flex box. New in CSS3
inherit = 				The value of the display property is inherited from parent element
inline =				Element is rendered as an inline element. This is default
inline-block =			Element is rendered as a block box inside an inline box
inline-flex =  			Element is rendered as a inline-level flex box. New in CSS3
inline-table = 			Element is rendered as an inline table (like <table>), with no line break before or after the table
list-item = 			Element is rendered as a list
marker = 				This value sets content before or after a box to be a marker (used with :before and :after pseudo-elements. Otherwise this value is identical to "inline")
none = 					Element will not be displayed
run-in = 				Element is rendered as block-level or inline element. Depends on context
table = 				Element is rendered as a block table (like <table>), with a line break before and after the table
table-caption = 		Element is rendered as a table caption (like <caption>)
table-cell = 			Element is rendered as a table cell (like <td> and <th>)
table-column = 			Element is rendered as a column of cells (like <col>)
table-column-group = 	Element is rendered as a group of one or more columns (like <colgroup>)
table-footer-group = 	Element is rendered as a table footer row (like <tfoot>)
table-header-group = 	Element is rendered as a table header row (like <thead>)
table-row = 			Element is rendered as a table row (like <tr>)
table-row-group	= 		Element is rendered as a group of one or more rows (like <tbody>)
initial =				Sets this property to its default value. Read about initial
inherit = 				Inherits this property from its parent element. Read about inherit
*/
	document.getElementById(nome).style.display = tp_display;
}


//##########################################################################################################
//														Pilha
//##########################################################################################################
function Pilha(){
    this.lista = new Array();

    this.inserir = function(obj){
        this.lista[this.lista.length] = obj;
    };

    this.removerUltimo = function(){
        if(this.lista.length > 0){
            var obj = this.lista[this.lista.length - 1];
            this.lista.splice(this.lista.length - 1,1);
            return obj;
        }else{
            alert("Não há objetos na pilha.");
        }
    };

    this.lerUltimo = function(){
        if(this.lista.length > 0){
            return this.lista[this.lista.length - 1];
        }else{
            alert("Não há objetos na pilha.");
        }
    };
}

//##########################################################################################################
//														Pilha
//##########################################################################################################
// ############ NOVO MODO POR LOCAL STORAGE ################
function set_cookie(name, value) {
	window.localStorage.setItem(name,value);
}

function get_cookie(chave){
	return window.localStorage.getItem(chave);
}


//##########################################################################################################
//													Ciptografia MD5
//##########################################################################################################
function md5(str) {
  //  discuss at: http://phpjs.org/functions/md5/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  // improved by: Michael White (http://getsprink.com)
  // improved by: Jack
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //    input by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //  depends on: utf8_encode
  //   example 1: md5('Kevin van Zonneveld');
  //   returns 1: '6e658d4bfcb59cc13f96c14450ac40b9'

  var xl;

  var rotateLeft = function(lValue, iShiftBits) {
    return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
  };

  var addUnsigned = function(lX, lY) {
    var lX4, lY4, lX8, lY8, lResult;
    lX8 = (lX & 0x80000000);
    lY8 = (lY & 0x80000000);
    lX4 = (lX & 0x40000000);
    lY4 = (lY & 0x40000000);
    lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
    if (lX4 & lY4) {
      return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
    }
    if (lX4 | lY4) {
      if (lResult & 0x40000000) {
        return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
      } else {
        return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
      }
    } else {
      return (lResult ^ lX8 ^ lY8);
    }
  };

  var _F = function(x, y, z) {
    return (x & y) | ((~x) & z);
  };
  var _G = function(x, y, z) {
    return (x & z) | (y & (~z));
  };
  var _H = function(x, y, z) {
    return (x ^ y ^ z);
  };
  var _I = function(x, y, z) {
    return (y ^ (x | (~z)));
  };

  var _FF = function(a, b, c, d, x, s, ac) {
    a = addUnsigned(a, addUnsigned(addUnsigned(_F(b, c, d), x), ac));
    return addUnsigned(rotateLeft(a, s), b);
  };

  var _GG = function(a, b, c, d, x, s, ac) {
    a = addUnsigned(a, addUnsigned(addUnsigned(_G(b, c, d), x), ac));
    return addUnsigned(rotateLeft(a, s), b);
  };

  var _HH = function(a, b, c, d, x, s, ac) {
    a = addUnsigned(a, addUnsigned(addUnsigned(_H(b, c, d), x), ac));
    return addUnsigned(rotateLeft(a, s), b);
  };

  var _II = function(a, b, c, d, x, s, ac) {
    a = addUnsigned(a, addUnsigned(addUnsigned(_I(b, c, d), x), ac));
    return addUnsigned(rotateLeft(a, s), b);
  };

  var convertToWordArray = function(str) {
    var lWordCount;
    var lMessageLength = str.length;
    var lNumberOfWords_temp1 = lMessageLength + 8;
    var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
    var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
    var lWordArray = new Array(lNumberOfWords - 1);
    var lBytePosition = 0;
    var lByteCount = 0;
    while (lByteCount < lMessageLength) {
      lWordCount = (lByteCount - (lByteCount % 4)) / 4;
      lBytePosition = (lByteCount % 4) * 8;
      lWordArray[lWordCount] = (lWordArray[lWordCount] | (str.charCodeAt(lByteCount) << lBytePosition));
      lByteCount++;
    }
    lWordCount = (lByteCount - (lByteCount % 4)) / 4;
    lBytePosition = (lByteCount % 4) * 8;
    lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
    lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
    lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
    return lWordArray;
  };

  var wordToHex = function(lValue) {
    var wordToHexValue = '',
      wordToHexValue_temp = '',
      lByte, lCount;
    for (lCount = 0; lCount <= 3; lCount++) {
      lByte = (lValue >>> (lCount * 8)) & 255;
      wordToHexValue_temp = '0' + lByte.toString(16);
      wordToHexValue = wordToHexValue + wordToHexValue_temp.substr(wordToHexValue_temp.length - 2, 2);
    }
    return wordToHexValue;
  };

  var x = [],
    k, AA, BB, CC, DD, a, b, c, d, S11 = 7,
    S12 = 12,
    S13 = 17,
    S14 = 22,
    S21 = 5,
    S22 = 9,
    S23 = 14,
    S24 = 20,
    S31 = 4,
    S32 = 11,
    S33 = 16,
    S34 = 23,
    S41 = 6,
    S42 = 10,
    S43 = 15,
    S44 = 21;

  str = this.utf8_encode(str);
  x = convertToWordArray(str);
  a = 0x67452301;
  b = 0xEFCDAB89;
  c = 0x98BADCFE;
  d = 0x10325476;

  xl = x.length;
  for (k = 0; k < xl; k += 16) {
    AA = a;
    BB = b;
    CC = c;
    DD = d;
    a = _FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
    d = _FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
    c = _FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
    b = _FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
    a = _FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
    d = _FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
    c = _FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
    b = _FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
    a = _FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
    d = _FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
    c = _FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
    b = _FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
    a = _FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
    d = _FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
    c = _FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
    b = _FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
    a = _GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
    d = _GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
    c = _GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
    b = _GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
    a = _GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
    d = _GG(d, a, b, c, x[k + 10], S22, 0x2441453);
    c = _GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
    b = _GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
    a = _GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
    d = _GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
    c = _GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
    b = _GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
    a = _GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
    d = _GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
    c = _GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
    b = _GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
    a = _HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
    d = _HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
    c = _HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
    b = _HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
    a = _HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
    d = _HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
    c = _HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
    b = _HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
    a = _HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
    d = _HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
    c = _HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
    b = _HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
    a = _HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
    d = _HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
    c = _HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
    b = _HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
    a = _II(a, b, c, d, x[k + 0], S41, 0xF4292244);
    d = _II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
    c = _II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
    b = _II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
    a = _II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
    d = _II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
    c = _II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
    b = _II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
    a = _II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
    d = _II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
    c = _II(c, d, a, b, x[k + 6], S43, 0xA3014314);
    b = _II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
    a = _II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
    d = _II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
    c = _II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
    b = _II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
    a = addUnsigned(a, AA);
    b = addUnsigned(b, BB);
    c = addUnsigned(c, CC);
    d = addUnsigned(d, DD);
  }

  var temp = wordToHex(a) + wordToHex(b) + wordToHex(c) + wordToHex(d);

  return temp.toLowerCase();
}

function utf8_encode(argString) {
  // discuss at: http://phpjs.org/functions/utf8_encode/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: sowberry
  // improved by: Jack
  // improved by: Yves Sucaet
  // improved by: kirilloid
  // bugfixed by: Onno Marsman
  // bugfixed by: Onno Marsman
  // bugfixed by: Ulrich
  // bugfixed by: Rafal Kukawski
  // bugfixed by: kirilloid
  //   example 1: utf8_encode('Kevin van Zonneveld');
  //   returns 1: 'Kevin van Zonneveld'

  if (argString === null || typeof argString === 'undefined') {
    return '';
  }

  var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
  var utftext = '',
    start, end, stringl = 0;

  start = end = 0;
  stringl = string.length;
  for (var n = 0; n < stringl; n++) {
    var c1 = string.charCodeAt(n);
    var enc = null;

    if (c1 < 128) {
      end++;
    } else if (c1 > 127 && c1 < 2048) {
      enc = String.fromCharCode(
        (c1 >> 6) | 192, (c1 & 63) | 128
      );
    } else if ((c1 & 0xF800) != 0xD800) {
      enc = String.fromCharCode(
        (c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    } else { // surrogate pairs
      if ((c1 & 0xFC00) != 0xD800) {
        throw new RangeError('Unmatched trail surrogate at ' + n);
      }
      var c2 = string.charCodeAt(++n);
      if ((c2 & 0xFC00) != 0xDC00) {
        throw new RangeError('Unmatched lead surrogate at ' + (n - 1));
      }
      c1 = ((c1 & 0x3FF) << 10) + (c2 & 0x3FF) + 0x10000;
      enc = String.fromCharCode(
        (c1 >> 18) | 240, ((c1 >> 12) & 63) | 128, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    }
    if (enc !== null) {
      if (end > start) {
        utftext += string.slice(start, end);
      }
      utftext += enc;
      start = end = n + 1;
    }
  }

  if (end > start) {
    utftext += string.slice(start, stringl);
  }

  return utftext;
}

//##########################################################################################################
//														Buscar Parâmetro
//##########################################################################################################

function buscar_parametro(parameter){
	var loc = location.search.substring(1, location.search.length);
    var param_value = false;
    var params = loc.split("&");
    for (i=0; i<params.length;i++){
    	param_name = params[i].substring(0,params[i].indexOf('='));
    	if (param_name == parameter){
    		param_value = params[i].substring(params[i].indexOf('=')+1)
    	}
    }
    if (param_value){
    	return param_value;
    }
    else{
    	return false;
    }
}

//##########################################################################################################################
//														Transpor Vetor
//##########################################################################################################################
function transpor_vet(vetor){
	var novo_vetor = [];
	var qtde_atual = 0;
	if(vetor.length>0){
		for (var x=0; x<vetor[0].length; x++ ){  //for each row
				novo_vetor[qtde_atual] = [];
			for (var y=0; y<vetor.length; y++){ //for each column
				novo_vetor[qtde_atual].push(vetor[y][x]);
			}
			++qtde_atual;
		}
		return novo_vetor;
	}else{
		return false;
	}
}
//##########################################################################################################
//														Breadcrumb
//##########################################################################################################
/*
-No HTML:
<div class="row">
	<div class="col s12">
		<div id="faixa_bread" class="col s12 blue-text text-darken-2">
			<a href="#!" class="breadcrumb blue-text text-darken-2" ><i class="material-icons" style="margin-left:0px;margin-top:5px;">store</i></a>
		</div>
	</div>
</div>

-Colocando espaço de margem no primeiro item da faixa através de js
-Evento on onload:
window.onload = function(){
	var teste = document.getElementsByClassName("breadcrumb")[0];
	teste.style.marginLeft = "20px";
}

-Alterar cor no CSS:
.breadcrumb{
	top: 5px;
	font-size: 15px;
}

.breadcrumb::before{
    color: rgba(38, 101, 195, 0.7);
	vertical-align: middle;
}
*/

function breadcrumb(){
    this.lista = new Array();

    this.inserir = function(destino, label){
		this.lista[this.lista.length] = label;
		localStorage.setItem('breadcrumb',JSON.stringify(this.lista));
		this.atualizar(destino);
    };

    this.removerUltimo = function(){
        if(this.lista.length > 0){
            var obj = this.lista[this.lista.length - 1];
            this.lista.splice(this.lista.length - 1,1);
			localStorage.setItem('breadcrumb',JSON.stringify(obj));
			//console.log(localStorage.setItem('breadcrumb'));
			this.atualizar();
            //return obj;
        }else{
            Console.log("Não há objetos na pilha.");
        }
    };

	this.removerApos = function(obj_rem){
        if(this.lista.length > 0){
			for(j=0;j<this.lista.length;j++){
				var obj = this.lista[j];
				if(obj == obj_rem){
					break;
				}
			}
			this.lista.splice(j+1,this.lista.length-j);
			localStorage.setItem('breadcrumb',JSON.stringify(obj));
			//console.log(localStorage.setItem('breadcrumb'));
			this.atualizar();
            //return obj;
        }else{
            Console.log("Não há objetos na pilha.");
        }
    };

    this.lerUltimo = function(){
        if(this.lista.length > 0){
            return this.lista[this.lista.length - 1];
        }else{
            Console.log("Não há objetos na pilha.");
        }
    };

	this.atualizar = function(destino){
		var bread = document.getElementById("faixa_bread");

		while(bread.hasChildNodes()){
			bread.removeChild(bread.lastChild);
		}

		//Adiciona o botão Home
		var a1 = document.createElement('a');
		var i1 = document.createElement('i');
		var nome_icone = document.createTextNode("store");
		i1.setAttribute("class", "material-icons");
		i1.setAttribute("style", "margin-left:10px;margin-top:5px;");
		i1.appendChild(nome_icone);
		a1.setAttribute("href", "#");
		a1.setAttribute("class", "breadcrumb brown-text text-darken-3");
		a1.setAttribute("onclick", "goTo('loja.html');return false;");
		a1.appendChild(i1);
        texto = document.createTextNode("Loja");
        a1.appendChild(texto);
		bread.appendChild(a1);

		//Adiciona cada elemento do vetor
		for(j=0;j<this.lista.length;j++){
			var a2 = document.createElement('a');
			var texto_link = document.createTextNode(this.lista[j]);
			a2.setAttribute("href", "#");
			a2.setAttribute("class", "breadcrumb brown-text text-darken-3");
			a2.setAttribute("onclick", "goTo_back('"+destino+"');return false;");
			a2.appendChild(texto_link);
			bread.appendChild(a2);
		}

	};
}

function goTo(destino){
    window.location.replace(destino);
}

function goTo_addbread(destino, label){
    fx_bread.inserir(destino, label);
    window.location.replace(destino);
}

function goTo_addbread_after(destino, label, manter){
    fx_bread.removerApos(manter);
    fx_bread.inserir(destino, label);
    window.location.replace(destino);
}

function goTo_back(manter){
    fx_bread.removerApos(manter);
}

//##########################################################################################################
//														Valida CPF
//##########################################################################################################

function valida_cpf(strCPF) {
    var cpf = right("00000000000" + apenasNumeros(strCPF),11);
    if(cpf == ''){
        return false;
    }
    // Elimina CPFs invalidos conhecidos
    if (cpf.length != 11 ||
        cpf == "00000000000" ||
        cpf == "11111111111" ||
        cpf == "22222222222" ||
        cpf == "33333333333" ||
        cpf == "44444444444" ||
        cpf == "55555555555" ||
        cpf == "66666666666" ||
        cpf == "77777777777" ||
        cpf == "88888888888" ||
        cpf == "99999999999"){
        Materialize.toast("Por favor, verifique seu CPF", 4000);
        erroValidacao = 1;
        return false;
    }

    // Valida 1o digito
    add = 0;
    for (i=0; i < 9; i ++)
        add += parseInt(cpf.charAt(i)) * (10 - i);
        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11)
            rev = 0;
        if (rev != parseInt(cpf.charAt(9))){
            Materialize.toast("Por favor, verifique seu CPF", 4000);
            erroValidacao = 1;
            return false;
        }

    // Valida 2o digito
    add = 0;
    for (i = 0; i < 10; i ++)
        add += parseInt(cpf.charAt(i)) * (11 - i);
        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(10))){
        Materialize.toast("Por favor, verifique seu CPF", 4000);
		erroValidacao = 1;
        return false;
    }
    return true;
}

//##########################################################################################################
//												Máscara CPF
//##########################################################################################################
function cpf_mask(v){
	v=v.replace(/\D/g,"");                 //Remove tudo o que não é dígito
	v=v.replace(/(\d{3})(\d)/,"$1.$2");    //Coloca ponto entre o terceiro e o quarto dígitos
	v=v.replace(/(\d{3})(\d)/,"$1.$2");    //Coloca ponto entre o setimo e o oitava dígitos
	v=v.replace(/(\d{3})(\d)/,"$1-$2");   //Coloca ponto entre o decimoprimeiro e o decimosegundo dígitos
	return v;
}

//##########################################################################################################
//												Máscara Celular
//##########################################################################################################
function celular(v){
    v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito
    v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
    v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos
    return v;
}

//##########################################################################################################
//												Máscara Genérica
//##########################################################################################################
function mascara(t, mask){
	var i = t.value.length;
	var saida = mask.substring(1,0);
	var texto = mask.substring(i)
	if (texto.substring(0,1) != saida){
		t.value += texto.substring(0,1);
	}
}

//##########################################################################################################
//											Deixar apenas números
//##########################################################################################################
function apenasNumeros(string){
    var numsStr = string.replace(/[^0-9]/g,'');
    return parseInt(numsStr);
}

//##########################################################################################################
//  										Minimo 8 dígitos  para senha
//##########################################################################################################
function valida_senha(campo){
	if (campo.length < 8){
        Materialize.toast("Olá, para sua segurança a senha deve conter pelo menos 8 caracteres. =)", 4000);
		erroValidacao = 1;
        return false;
	}else{
        return true;
    }
}
//##########################################################################################################
//										Minimo 14 dígitos para telefone
//##########################################################################################################
function valida_telefone(campo){
	if (campo.length < 14){
        Materialize.toast("Por favor preencha seu telefone corretamente. (ddd) xxxxx-xxxx.", 4000);
		erroValidacao = 1;
        return false;
	}else{
        return true;
    }
}

//##########################################################################################################
//											Vê se o nome é completo
//##########################################################################################################
//Função genérica de validação do nome

function valida_nome(campo){
	var string_inicial = campo;
	var expressao_espacos = /  +/;
	var expressao_espacos_inicio = /^ /;
	var expressao_espacos_final = / $/;
	var expressao_final = /[a-z]* [a-z]*/;
	var string_final=string_inicial;
	//alert("string inicial:"+string_inicial);
	while (expressao_espacos.test(string_final)===true){
		string_final = string_final.replace(expressao_espacos,' ');
	}
	if(expressao_espacos_inicio.test(string_final)===true){
		string_final = string_final.replace(expressao_espacos_inicio,'');
	}
	if(expressao_espacos_final.test(string_final)===true){
		string_final = string_final.replace(expressao_espacos_final,'');
	}
	//alert("string final:"+string_final);
	if(!expressao_final.test(string_final) || string_final.length<3){
        Materialize.toast("Opa! Digite seu nome completo ;-)", 4000);
		erroValidacao = 1;
        return false;
	}else{
		var array_nome = new Array();
		array_nome = string_final.split(' ');
		var var_nome = array_nome[0];
		var var_sobrenome='';
		var i;
		for (i = 1; i < array_nome.length; i++){
			if(i===1){
				var_sobrenome += array_nome[i];
			}else{
				var_sobrenome += ' ' + array_nome[i];
			}
		}
		var array_nome_return = [];
		array_nome_return[0] = var_nome;
		array_nome_return[1] = var_sobrenome;
		return array_nome_return;
	}
}

//##########################################################################################################
//												Função Left
//##########################################################################################################
function left(str, n){
    if (n <= 0)
        return "";
    else if (n > String(str).length)
        return str;
    else
        return String(str).substring(0,n);
}

//##########################################################################################################
//												Função Right
//##########################################################################################################
function right(str, n){
    if (n <= 0)
        return "";
    else if (n > String(str).length)
        return str;
    else {
        var iLen = String(str).length;
        return String(str).substring(iLen, iLen - n);
    }
}

//##########################################################################################################################
//										Wait Me (exibe spinner enquanto carrega alguma ação)
//##########################################################################################################################
function run_waitMe(el, num, effect){
		/*
		bounce, rotateplane, stretch, orbit, roundBounce,
		win8, win8_linear, ios, facebook, rotation, timer,
		pulse, progressBar, bouncePulse, img
		*/
		text = 'Carregando...';
		fontSize = '';
		switch (num) {
			case 1:
			maxSize = '';
			textPos = 'vertical';
			break;
			case 2:
			text = '';
			maxSize = 30;
			textPos = 'vertical';
			break;
			case 3:
			maxSize = 30;
			textPos = 'horizontal';
			fontSize = '18px';
			break;
		}
		$("#wait").css("display", "block");
		$(el).waitMe({
			effect: effect,
			text: text,
			bg: 'rgba(255,255,255,0.7)',
			color: '#000',
			maxSize: maxSize,
			textPos: textPos,
			fontSize: fontSize,
			onClose: function() {}
		});
	}

    //##########################################################################################################################
    //									Seleciona todas as options de um select
    //##########################################################################################################################
        function selectAll(selectBox,selectAll) {
            // have we been passed an ID
            if (typeof selectBox == "string") {
                selectBox = document.getElementById(selectBox);
            }
            // is the select box a multiple select box?
            if(selectBox.type == "select-multiple") {
                for(var i = 0; i < selectBox.options.length; i++) {
                    selectBox.options[i].selected = selectAll;
                 }
            }
            $('select').material_select();
        }

    //##########################################################################################################################
    //									Função de LOG cross-browser
    //##########################################################################################################################
    function log(){
        try{
            console.log.apply(console,arguments);
        }
        catch(e){
            try{
                opera.postError.apply(opera, arguments);
            }
            catch(e){
                alert(Array.prototype.join.call(arguments, ""));
            }
        }
    }


//#########################################################################################################
//													FIM DO ARQUIVO...
//#########################################################################################################
