<?php
// source: D:\Programy\EasyPHP-DevServer-14.1VC11\data\localweb\jazykovka\app\presenters/templates/footer.latte

class Templatea501135a63437bc984403dab0052d54a extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('fe4fbc8190', 'html')
;
// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIMacros::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
?>
<footer class="container-fluid">
    &copy; Gaidalf s.r.o. 2015
</footer><?php
}}