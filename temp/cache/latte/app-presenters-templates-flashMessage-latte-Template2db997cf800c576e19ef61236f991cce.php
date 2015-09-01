<?php
// source: D:\Programy\EasyPHP-DevServer-14.1VC11\data\localweb\jazykovka\app\presenters/templates/flashMessage.latte

class Template2db997cf800c576e19ef61236f991cce extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('3df72cb182', 'html')
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
<div class="row">
    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4">
<?php $iterations = 0; foreach ($flashes as $flash) { ?>        <div class="alert alert-dismissible <?php echo Latte\Runtime\Filters::escapeHtml($flas->type === 'info' ? 'alert-success' : $flash->type, ENT_COMPAT) ?>">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <?php echo Latte\Runtime\Filters::escapeHtml($flash->message, ENT_NOQUOTES) ?>

        </div>
<?php $iterations++; } ?>
    </div>
</div><?php
}}