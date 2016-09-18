<?php 
$this->html->addCss('m3_default');
$this->insert ('m3_default_header');
?>
<div id="header"></div>
<script type="application/javascript">
/*base = m3.id("header")

d = m3.c("div")
d.style = 'width: 100px; height: 100px; background: white;'
base.appendChild(d)*/
</script>
<?php
$this->insert ('m3_messages');
$this->content();
$this->insert ('m3_default_footer');
?>