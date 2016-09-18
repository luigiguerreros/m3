<?php 
namespace welcome; use M3;

$this->setLayout('m3_default');
$this['_m3_page_title'] = 'Welcome to your new M3 project!';
?>
<div class="container">
<h1>Welcome to your new M3 project!</h1>
<p>Now, you have to do a few things more before start:</p>

<ul>
<li>Adjust the <tt>config/settings.php</tt> file to your needs (specially in the <tt>databases</tt> section, if you'll use one) .</li>

<li>Create new applications for your project. You can use the <tt>m3</tt> script for this task:</li>

<pre>cd <?=M3\PROJECT_PATH?>

m3 new app my_app</pre>

<li>Edit the <tt>config/routes.php</tt> file for setting the default app for
this project (it will be shown when there is no app specified in the url, 
replacing this page). Add this line inside the array:

<pre>
(new Rule)->default('my_app'),
</pre>
</li>
<li>And that's it. Have fun coding!</li>

</ul>
</div>