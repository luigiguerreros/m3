<?php use m3\view, m3\html;

// Si no hay un título, creamos uno por defecto 
if (!$this['_m3_page_title']) {

    // Ignoramos el controller si es 'default'
    $this['_m3_page_title'] = ucfirst (M3::$application);

    if (M3::$controller != 'default') {
        $this['_m3_page_title'] .= ' / ' . ucfirst(M3::$controller);
    }
}

// Modificamos el título con un callable, si queremos
if (is_callable( $this['_m3_page_title_callable'])) {

    // PHP quirk...
    $page_title = $this['_m3_page_title_callable'];
    $this['_m3_page_title'] = $page_title();
}

// Charset por defecto
$this->html->addMeta('charset', 'utf-8');

// Token de seguridad
$this->html->addMeta(['m3-security-token' => M3\Csrf::$token]);

// Assets por defecto
$this->html->addDefaultAssets($this->name);

// Y añadimos el CSS del m3 AL INICIO 
$this->html->prependCss ('m3');

// También un javascript al inicio
$this->html->prependScript ('m3');

?>
<!DOCTYPE html>
<html>
<head>
<base href="<?=M3::$base_url?>" />
<title><?=$this['_m3_page_title']?></title>
<?=$this->html->drawMeta()?>
<?=$this->html->drawLink()?>
<?=$this->html->drawScripts()?>
</head>
<body>
