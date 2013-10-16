<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="UTF-8">
    <title>Total Respawn</title>
	<link href="style.css" rel="stylesheet">
	<link rel="shortcut icon" type="/image/png" href="favicon2.png">
		<!--[if IE]><script> document.createElement("article");document.createElement("aside");document.createElement("section");document.createElement("footer");</script> <![endif]-->
</head>
<body>
	<?php
	// functions
	function store($file,$datas){file_put_contents($file,serialize($datas));}
	function unstore($file){ return unserialize(file_get_contents($file));}
	function aff($a,$stop=true){echo 'Arret a la ligne '.__LINE__.' du fichier '.__FILE__.'<pre>';var_dump($a);echo '</pre>';if ($stop){exit();}}
	function returndomain($url){$domaine=parse_url($url);return $domaine['host'];}
	function tag2links($tagstring){
		global $GLOBAL;
		$array=explode(' ',$tagstring);$links='';
		foreach ($array as $tag){
			$links.='<a href="'.$GLOBAL['respawn_url'].'?tag='.$tag.'" class="tag">'.$tag.'</a>';
		}
		return $links;
	}
	function search($array,$tag=false){	
		$result=array();
		if (!$tag){return $array;}
		else{
			foreach ($array as $key=>$val){
				foreach($val['url'] as $key2=>$val2){
					if (isset($val['tags'][$key2])&&stripos($val['tags'][$key2],$tag)!==false){$result[$key]=$val;}
				}
			}
			return $result;
		}
	}


	$links=unstore('respawn_pages_links.txt');
	if (!is_array($links)){$links=array();store('respawn_pages_links.txt',$links);}
	if ($_POST){
		$url=strip_tags($_POST['url']).'?api';
		if (array_search($url, $links)===false){
			$var=@file_get_contents($url);
			$var=@unserialize($var);
			if ($var===false){
				echo '<p class="error">erreur, adresse non valide ou version de respawn <2.0</p>';
			}else {			
				$links[]=$url;
				store('respawn_pages_links.txt',$links);
			}
		}else{
			echo '<p class="error">erreur, l\'adresse est dans la base</p>';
		}
	}

	if (isset($_GET['tag'])){$search_tag=strip_tags($_GET['tag']);}else{$_GET['tag']='';}

	?>
	<header>
		<img src="favicon2.png"/> TotalRespawn: l'annuaire des respawns
	</header>
	<div class="list">
	<?php
		$all_respawns=array();
		foreach ($links as $link){ // récupération et organisation des différentes pages respawnées 
			$var=file_get_contents($link);$var=@unserialize($var);
			if ($var){
				foreach ($var as $item){
					$key=strip_tags($item['original_link']);
					$all_respawns[$key]['title']=strip_tags($item['title']);
					$all_respawns[$key]['url'][]=strip_tags($item['respawn_link']);
					$all_respawns[$key]['date'][]=strip_tags($item['date']);
					if (isset($item['tags'])){$all_respawns[$key]['tags'][]=strip_tags($item['tags']);}
				}
				
			}
		}

	if (!empty($search_tag)){$all_respawns=search($all_respawns,$search_tag);}// apply search if there's a query

	foreach($all_respawns as $key=>$item){
		echo '<fieldset onClick="toggle(this);"><h1>'.$item['title'].' <em>('.count($item['url']).')</em></h1>'."\n";
		echo '<li class="origine"><a href="'.$key.'">Page d\'origine </a></li>'."\n";
		foreach($item['url'] as $key=>$url){
			if (isset($item['tags'][$key])){$taglist=tag2links($item['tags'][$key]);}else{$taglist='';}
			echo '<li><a href="'.$url.'">'.$item['title'].'</a> chez <em>'.returndomain($url).' le '.$item['date'][$key].'</em> '.$taglist.'</li>'."\n";
		}
		echo '</fieldset>'."\n";
	}


	?>
	</div>



	<div class ="links">
		<h1>Respawns listés</h1>
	<ul class="respawns_list">
	<?php
	 foreach ($links as $link){
	 	echo '<li><a href="'.str_replace('?api','',$link).'">'.returndomain($link).'</a></li>';
	 }
	?>
	</ul>
	<form action="#" method="post">
		<input type="text" name="url" placeholder="Adresse de votre page respawn"/>
		<input type="submit"/>
	</form>

	</div>


	<footer>Codé avec amour par <a href="http://warriordudimanche.net">Bronco</a> en licence <em>fait'z'en quoi vous voulez</em></footer>
	<script>
		function toggle(obj){
			height='25px';
			if(obj.style.height==height||obj.style.height==''){obj.style.height="auto";}
			else{obj.style.height=height;}
		}
	</script>
	</body>
</html>
