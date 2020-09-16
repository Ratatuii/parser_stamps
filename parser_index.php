<?php
for($j=1941;$j<=1945;$j++){

for($i=1;$i<=4;$i++){
	
	// URL с которого нужно скачать изображения.
	$url = "https://filtorg.ru/marki/sssr/$j/page-$i/";
	
	// Директория куда будут сохранятся изображения.
	$path = dirname(__FILE__) . '\download'."\\$j";
// var_dump(__FILE__);
// Загружать или нет изображения с других доменов.
$external = true;

$html = file_get_contents($url);
preg_match_all('/<img.*?src=["\'](.*?)["\'].*?>/i', $html, $images, PREG_SET_ORDER);

$url = parse_url($url);
$path = rtrim($path, '/');

foreach ($images as $image) {
	if (strpos($image[1], 'data:image/') !== false) {
		continue;
	}
	
	// var_dump($image[1]);
	// echo '<br>';
	// Приклеивает протокол http к ссылкам $image[1]
	if (substr($image[1], 0, 2) == '//') {
		$image[1] = 'http:' . $image[1];
	}
	//переменная $ext забирает тип картинки по ссылке
	$ext = strtolower(substr(strrchr($image[1], '.'), 1));
	// var_dump($ext);
	// echo '<br>';
	if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
		//$img это ассоциативный массив из частей ссылки,  $img['path'] путь после домена
		$img = parse_url($image[1]);
		// Если файл уже существует  
		if (is_file($path . strrchr($img['path'], '/'))) {
			continue;
		}
		// var_dump($img['path']);
		// echo '<br>';
		//полный путь на диске в папке где будет сохранён файл
		$path_img = $path;
		// dirname($img['path']) это путь по ссылке без домена и конечного файла в строке;
		if (!is_dir($path_img)) {
			mkdir($path_img, 0777, true);
		}
		if (empty($img['host']) && !empty($img['path'])) {
			copy($url['scheme'] . '://' . $url['host'] . $img['path'], $path .strrchr($img['path'], '/'));
		} elseif ($external || ($external == false && $img['host'] == $url['host'])) {
			copy($image[1], $path . strrchr($img['path'], '/'));
		}
	}
}
}
}
echo "Конец";
?>