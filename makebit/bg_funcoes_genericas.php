<?php
	ini_set('display_errors','on');
	error_reporting(E_ALL);

    //#####################################################################################################
    //                                                Transpor Vetor
    //#####################################################################################################
	function transpor_vet($vetor)
	{
		$novo_vetor=array();
		$qtde_atual = 0;
		if(count($vetor)){
			for($x=0; $x<max(array_map('count',  $vetor)); $x++){
				$novo_vetor[$qtde_atual] = array();
				for ($y=0; $y<count($vetor); $y++){
					array_push($novo_vetor[$qtde_atual],$vetor[$y][$x]);
					//echo $vetor[$y][$x] .'<br>';
				}
				//echo 'nova linha<br>';
				$qtde_atual++;
			}
			return $novo_vetor;
		}else{
			return false;
		}
	}

    //#####################################################################################################
    //                                                Valida CPF
    //#####################################################################################################
    function valida_cpf($cpf=false){
        // Exemplo de CPF: 025.462.884-23 (precisa receber nesse formato)

        /**
         * Multiplica dígitos vezes posições
         *
         * @param string $digitos Os digitos desejados
         * @param int $posicoes A posição que vai iniciar a regressão
         * @param int $soma_digitos A soma das multiplicações entre posições e dígitos
         * @return int Os dígitos enviados concatenados com o último dígito
         *
         */
        if ( ! function_exists('calc_digitos_posicoes') ) {
            function calc_digitos_posicoes( $digitos, $posicoes = 10, $soma_digitos = 0 ) {
                // Faz a soma dos dígitos com a posição
                // Ex. para 10 posições:
                //   0    2    5    4    6    2    8    8   4
                // x10   x9   x8   x7   x6   x5   x4   x3  x2
                //   0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
                for ( $i = 0; $i < strlen( $digitos ); $i++  ) {
                    $soma_digitos = $soma_digitos + ( $digitos[$i] * $posicoes );
                    $posicoes--;
                }

                // Captura o resto da divisão entre $soma_digitos dividido por 11
                // Ex.: 196 % 11 = 9
                $soma_digitos = $soma_digitos % 11;

                // Verifica se $soma_digitos é menor que 2
                if ( $soma_digitos < 2 ) {
                    // $soma_digitos agora será zero
                    $soma_digitos = 0;
                } else {
                    // Se for maior que 2, o resultado é 11 menos $soma_digitos
                    // Ex.: 11 - 9 = 2
                    // Nosso dígito procurado é 2
                    $soma_digitos = 11 - $soma_digitos;
                }

                // Concatena mais um dígito aos primeiro nove dígitos
                // Ex.: 025462884 + 2 = 0254628842
                $cpf = $digitos . $soma_digitos;

                // Retorna
                return $cpf;
            }
        }

        // Verifica se o CPF foi enviado
        if ( ! $cpf ) {
            return false;
        }

        // Remove tudo que não é número do CPF
        // Ex.: 025.462.884-23 = 02546288423
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

        // Verifica se o CPF tem 11 caracteres
        // Ex.: 02546288423 = 11 números
        if ( strlen( $cpf ) != 11 ) {
            return false;
        }

        // Captura os 9 primeiros dígitos do CPF
        // Ex.: 02546288423 = 025462884
        $digitos = substr($cpf, 0, 9);

        // Faz o cálculo dos 9 primeiros dígitos do CPF para obter o primeiro dígito
        $novo_cpf = calc_digitos_posicoes( $digitos );

        // Faz o cálculo dos 10 dígitos do CPF para obter o último dígito
        $novo_cpf = calc_digitos_posicoes( $novo_cpf, 11 );

        // Verifica se o novo CPF gerado é idêntico ao CPF enviado
        if ( $novo_cpf === $cpf ) {
            // CPF válido
            return true;
        } else {
            // CPF inválido
            return false;
        }
    }


    //###############################################################################################
    //                             Varre todos os GETs da URL e salva em um array
    //###############################################################################################
    function get_all_get(){
        $output=[];
        foreach($_GET as $key=>$val){
            $output[$key] = $val;
        }
        return $output;
    }

    //###############################################################################################
    //                                                Valida CNPJ
    //###############################################################################################
    function valida_cnpj($cnpj){
        // Deixa o CNPJ com apenas números
        $cnpj = preg_replace( '/[^0-9]/', '', $cnpj );

        // Garante que o CNPJ é uma string
        $cnpj = (string)$cnpj;

        // O valor original
        $cnpj_original = $cnpj;

        // Captura os primeiros 12 números do CNPJ
        $primeiros_numeros_cnpj = substr( $cnpj, 0, 12 );

        /**
         * Multiplicação do CNPJ
         *
         * @param string $cnpj Os digitos do CNPJ
         * @param int $posicoes A posição que vai iniciar a regressão
         * @return int O
         *
         */
        if ( ! function_exists('multiplica_cnpj') ) {
            function multiplica_cnpj( $cnpj, $posicao = 5 ) {
                // Variável para o cálculo
                $calculo = 0;

                // Laço para percorrer os item do cnpj
                for ( $i = 0; $i < strlen( $cnpj ); $i++ ) {
                    // Cálculo mais posição do CNPJ * a posição
                    $calculo = $calculo + ( $cnpj[$i] * $posicao );

                    // Decrementa a posição a cada volta do laço
                    $posicao--;

                    // Se a posição for menor que 2, ela se torna 9
                    if ( $posicao < 2 ) {
                        $posicao = 9;
                    }
                }
                // Retorna o cálculo
                return $calculo;
            }
        }

        // Faz o primeiro cálculo
        $primeiro_calculo = multiplica_cnpj( $primeiros_numeros_cnpj );

        // Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
        // Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
        $primeiro_digito = ( $primeiro_calculo % 11 ) < 2 ? 0 :  11 - ( $primeiro_calculo % 11 );

        // Concatena o primeiro dígito nos 12 primeiros números do CNPJ
        // Agora temos 13 números aqui
        $primeiros_numeros_cnpj .= $primeiro_digito;

        // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
        $segundo_calculo = multiplica_cnpj( $primeiros_numeros_cnpj, 6 );
        $segundo_digito = ( $segundo_calculo % 11 ) < 2 ? 0 :  11 - ( $segundo_calculo % 11 );

        // Concatena o segundo dígito ao CNPJ
        $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

        // Verifica se o CNPJ gerado é idêntico ao enviado
        if ( $cnpj === $cnpj_original ) {
            return true;
        }else{
            return false;
        }
    }


    //###############################################################################################
    //                                                String to float
    //###############################################################################################
    function tofloat($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }


    //###############################################################################################
    //                            Limpar caracteres especiais de uma string
    //###############################################################################################
    function cleanString($text) {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/[ṕ]/u'        =>   'p',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }

    function med_arr($vetor){
        $sum_vetor = 0;
        $count_vetor = 0;
        $resp = 0;

        //Percorre vetor original
        foreach ($vetor as $key => $value) {
            $sum_vetor = $sum_vetor + $value;
            $count_vetor = $count_vetor + 1;
        }

        $resp = ($sum_vetor/$count_vetor);
        return $resp;
    }

    function sum_arr($vetor){
        $sum_vetor = 0;
        $resp = 0;

        //Percorre vetor original
        foreach ($vetor as $key => $value) {
            $sum_vetor = $sum_vetor + $value;
        }

        $resp = $sum_vetor;
        return $resp;
    }

    function count_arr($vetor){
        $count_vetor = 0;
        $resp = 0;

        //Percorre vetor original
        foreach ($vetor as $key => $value) {
            $count_vetor = $count_vetor + 1;
        }

        $resp = $count_vetor;
        return $resp;
    }


    function max_arr($vetor){
        $valor_selecionado = 0;
        $posicao_selecionada = 0;
        $count_vetor = 0;
        $primeiro = 0;
        $resp = 0;

        //Percorre vetor original
        foreach ($vetor as $key => $value) {
            if($primeiro==0){
                $valor_selecionado = $value;
                $primeiro = 1;
            }
            if($value>$valor_selecionado){
                $valor_selecionado = $value;
                $posicao_selecionada = $count_vetor;
            }
            $count_vetor = $count_vetor + 1;
        }

        $resp = $valor_selecionado;
        return $resp;
    }


    function min_arr($vetor){
        $valor_selecionado = 0;
        $posicao_selecionada = 0;
        $count_vetor = 0;
        $primeiro = 0;
        $resp = 0;

        //Percorre vetor original
        foreach ($vetor as $key => $value) {
            if($primeiro==0){
                $valor_selecionado = $value;
                $primeiro = 1;
            }
            if($value<$valor_selecionado){
                $valor_selecionado = $value;
                $posicao_selecionada = $count_vetor;
            }
            $count_vetor = $count_vetor + 1;
        }

        $resp = $valor_selecionado;
        return $resp;
    }

    function first_arr($vetor){
        $valor_selecionado = 0;

        $primeiro = 0;
        $resp = 0;

        //Percorre vetor original
        foreach ($vetor as $key => $value) {
            if($primeiro==0){
                $valor_selecionado = $value;
                $primeiro = 1;
                break;
            }
        }

        $resp = $valor_selecionado;
        return $resp;
    }

    function last_arr($vetor){
        $valor_selecionado = 0;

        $resp = 0;

        //Percorre vetor original
        foreach ($vetor as $key => $value) {
                $valor_selecionado = $value;
        }

        $resp = $valor_selecionado;
        return $resp;
    }

	function agrupa_tempo($unidade, $quantidade, $vetor_tempo){
		//echo "vetor tempo: <br>";
		//var_dump($vetor_tempo);
		$vetor_tempo_agrup = [];
		switch ($unidade) {
			case 'minutos':
				foreach ($vetor_tempo as $key => $value) {
					//echo $tempo_agrup . "<br>";
					array_push($vetor_tempo_agrup,date("Y-m-d H", strtotime($value)) . ':' . (intval(date("i", strtotime($value))/$quantidade)*$quantidade));
				}
				return $vetor_tempo_agrup;
				break;
			case 'horas':
				foreach ($vetor_tempo as $key => $value) {
					//echo $tempo_agrup . "<br>";
					array_push($vetor_tempo_agrup,date("Y-m-d", strtotime($value)) . ' ' . (intval(date("H", strtotime($value))/$quantidade)*$quantidade) . ':00');
				}
				return $vetor_tempo_agrup;
				break;
			case 'dias':
				foreach ($vetor_tempo as $key => $value) {
					//echo $tempo_agrup . "<br>";
					array_push($vetor_tempo_agrup,date("Y-m", strtotime($value)) . '-' . (intval(date("d", strtotime($value))/$quantidade)*$quantidade));
				}
				return $vetor_tempo_agrup;
				break;
			case 'meses':
				foreach ($vetor_tempo as $key => $value) {
					//echo $tempo_agrup . "<br>";
					array_push($vetor_tempo_agrup,date("Y", strtotime($value)) . '-' . (intval(date("m", strtotime($value))/$quantidade)*$quantidade));
				}
				return $vetor_tempo_agrup;
				break;
			case 'anos':
				foreach ($vetor_tempo as $key => $value) {
					//echo $tempo_agrup . "<br>";
					array_push($vetor_tempo_agrup,(intval(date("Y", strtotime($value))/$quantidade)*$quantidade));
				}
				return $vetor_tempo_agrup;
				break;
			default:
				echo "Favor informar unidade válida: minutos, horas, dias, meses, anos;";
				break;
		}
	}


	//Funções de controle de texto (Strings)
	function str_toda_maiuscula($texto){
		return strtoupper($texto);
	}
	function str_toda_minuscula($texto){
		return strtolower($texto);
	}
	function str_primeiras_palavras_maisculas($texto){
		return ucwords(strtolower($texto));
	}
	function str_primeira_letra_maiuscula($texto){
		return ucfirst(strtolower($texto));
	}
	function str_subst_caracteres($txt_procurado,$txt_novo,$txt_procurado){
		return str_replace($txt_procurado,$txt_novo,$txt_procurado);
	}
	function str_zero_esquerda($txt){
		return str_pad($txt, sizeof($txt)+1,0,STR_PAD_LEFT);
	}
	function str_repetir($txt, $qtde){
		return str_repeat($txt,$qtde);
	}

	//Converter arrays associativos (["fruta"=>"banana"]) em variáveis:
	//extract($nome_do_array);
	//echo "{$fruta}<br>";


	//Remover tags de script dentro do array:
	//$novo_array = array_map('strip_tags',$array);

	//Remover espaços dentro do array:
	//$novo_array = array_map('trim',$array);
?>
