<!DOCTYPE html>
	<html lang="es" >
	<head>
		<title>Install error</title>
		<style type="text/css">
			* {
				margin: 0;
				padding: 0;
			}
			body{
			    font-family: Arial, Helvetica, sans-serif; /* Base font family for most elements */
			    font-size: 0.8em; /* Base size for all elements; other size definitions relates to this */
			    background-color: #ffffff;
			    background: url(./install/vista/img/bg_cuadricula.png) 0 0 repeat;
			}
			h1{
			    font-size: 25px;
			    border-bottom: 1px solid #000;
			}
			article{
			    width: 750px;
			    margin: 0 auto 0 auto;
			    background-color: #ffffff;
			}
			section{
				margin:20px;
			}
			p{
				margin:10px 0px;
			}
			footer{ 
			    clear: both;
				text-align: right;
				width:100%;
				color:#bbb
			}
			label{
				font-weight: bold;
				display:block;
				clear:both;
			}
		</style>
	</head>
	<body>
		<article>
		    <header></header>
			<h1>Install Error</h1>    
			<section>
	        	<? $error = error_get_last(); ?>
	        	
	        	<p><label> message</label>
	        	<?= $error["message"]?></p> 
	        	 
	        	<p><label>file</label>
	        	<?= $error["file"]?> line: <?= $error["line"] ?></p>
			</section>
		    
		    <footer><p>filmfestCMS</p></footer>
	    </article>
	</body>
</html>