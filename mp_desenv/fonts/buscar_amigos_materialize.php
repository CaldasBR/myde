<!doctype html>
<html>
<head>
	    
    <!-- TÍTULO DA PÁGINA -->    
	<title>Buscar amigos</title>
    
    
    <!-- INICIALIZAÇÃO DO MATERIALIZE -->
    
	<!--Import Google Icon Font-->
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    

	<!--Import jQuery before materialize.js-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script type="text/javascript" src="js/inicio_materialize.js"></script>
    
    <!--Import Font awesome-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    
        
    <!-- COLOCA ÍCONE NO TÍTULO DA PÁGINA -->
    <link rel="icon" type="image/jpg" href="media/imagens/favicon.jpg" />
    
    <!-- INICIALIZAÇÃO CSS PERSONALIZADO -->
	<link type="text/css" rel="stylesheet" href="/css/customizado_materialize.css"/>
    
    
    <!-- META CONFIGURAÇÕES / VIEWPORTS / MEDIA QUERYES -->    
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
      
</head>

<body>

	<!-- ## TELA PARA ULTIMAS CONVERSAS ## --> 
	
    <div class="card-panel white" data-role="page" id="page_login" class="valign-wrapper" style="padding-bottom:5em; min-height:50em;">
    	
        <!-- Header -->  
        
        <header>
        <div>
    		<nav class="header">
    			<div class="navbar-fixed" style="height:50px; background-color:#006bb5" >
      		    	<a href="#" class="brand-logo center" style="line-height:50px; font-size:16px">Buscar Pessoas</a>
      				<a class="imagem_voltar_tela"><img src="/media/imagens/voltar_seta_2.png" style="height: 15px;margin-bottom: 3px;"></a>
                    <a class="menu"><img src="/media/imagens/menu.png" style="height:20px"></a>
                    <a class="envelope"><img src="/media/imagens/envelope.png" style="height:18px;">3</a>
                </div>
        	</nav>
        </div>
        </header>
        
        <br>
     	
         <!-- Conteúdo  --> 
        <main>
        
     	<!-- Buscar por nome -->  
     	<div class="row">
				<form class="col s12" style="color: blue;">
					<div class="row" style="margin-top:-15px; margin-bottom:-15px;">
						<div class="input-field col s12">
							<i class="material-icons prefix" style="color:lightgray; top: 27px; font-size: 1.5em;">search</i>
							<input id="txt_name" type="text" class="validate" style= "border: 0.5px solid lightgray; border-radius: 1em; margin-top: 1em; margin-bottom: -1em;" required>
							<label for="txt_name" class="white-text">Nome</label>
						</div>
					</div>
				</form>
			</div>
        
     	<br>
     
      	<!-- Avatar boxes --> 
     
     	<ul class="collection">
    		<li class="collection-item avatar">
      			<img src="/media/imagens/tio_patinhas.jpg" alt="" class="circle">
      			<span class="title truncate" style="width: 88%;">Patrícia Oliveira</span>
      			<p class="truncate" style="width: 88%; color:gray">São Bernardo<br>
         			25 anos
      			</p>
            </li>
            <li class="collection-item avatar">
      			<img src="/media/imagens/tio_patinhas.jpg" alt="" class="circle">
      			<span class="title truncate" style="width: 88%;">Patrícia Oliveira</span>
      			<p class="truncate" style="width: 88%; color:gray">São Bernardo<br>
         			25 anos
      			</p>
            </li>
            <li class="collection-item avatar">
      			<img src="/media/imagens/tio_patinhas.jpg" alt="" class="circle">
      			<span class="title truncate" style="width: 88%;">Patrícia Oliveira</span>
      			<p class="truncate" style="width: 88%; color:gray">São Bernardo<br>
         			25 anos
      			</p>
            </li>
            <li class="collection-item avatar">
      			<img src="/media/imagens/tio_patinhas.jpg" alt="" class="circle">
      			<span class="title truncate" style="width: 88%;">Patrícia Oliveira</span>
      			<p class="truncate" style="width: 88%; color:gray">São Bernardo<br>
         			25 anos
      			</p>
            </li>
            <li class="collection-item avatar">
      			<img src="/media/imagens/tio_patinhas.jpg" alt="" class="circle">
      			<span class="title truncate" style="width: 88%;">Patrícia Oliveira</span>
      			<p class="truncate" style="width: 88%; color:gray">São Bernardo<br>
         			25 anos
      			</p>
            </li>
            <li class="collection-item avatar">
      			<img src="/media/imagens/tio_patinhas.jpg" alt="" class="circle">
      			<span class="title truncate" style="width: 88%;">Patrícia Oliveira</span>
      			<p class="truncate" style="width: 88%; color:gray">São Bernardo<br>
         			25 anos
      			</p>
            </li>
           
   		</ul>
    	
    	
        <!-- botão buscar -->  
        
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    		<a class="btn-floating btn-large orange waves-effect waves-light btn">
      			<i class="fa fa-filter" aria-hidden="true"></i>
            </a>
    	</div>
        
        </main>
        
        
        <!-- Footer -->  
	
    	<footer class="page-footer" style="background-color: #006bb5; bottom:0; height: 3em; left: 0px; position:fixed; width:100%; padding:0em;z-index:1">
       		<div class="center-align row">
            
       			<div class="col s6" Style="border: 0px solid #95A9B8; padding:0px;">
        			<a href="" class="waves-effect waves-light btn" style="width: 100%; padding: 0px; margin: 0px; height: 100%; 	background-color:#006bb5;">
                    	<i style="margin-top: 5px;" class="fa fa-user" aria-hidden="true"></i>
                    </a>
                </div>
                
                <div class="col s6" style="border: 0px solid #95A9B8; padding:0px;">
        			<a href="" class="waves-effect waves-light btn" style="width: 100%; padding: 0px; margin: 0px; height: 100%; 	background-color:#006bb5;">
                    	<i style="margin-top: 5px;" class="fa fa-map-marker" aria-hidden="true"></i>
                    </a>
        		</div>
       		</div>
    	</footer>
    	
    </div>
  
        
</body>

</html>