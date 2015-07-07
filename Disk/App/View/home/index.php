<?php
$data = [
    'title' => $title,
    'body_class' => 'bs-docs-home',
];
View::tplInclude('Public/header', $data); ?>
<main class="bs-docs-masthead" id="content" role="main">
    <div class="container">
        <h1><?php echo $title;?></h1>
        <p class="lead">单文件PHP框架，羽量级网站开发首选</p>
        <p>本项目由<a href='http://leo108.com' target='_blank'>leo108</a>开发，遵循MIT协议。</p>
        <p>
            <a href="https://github.com/leo108/SinglePHP" target='_blank' class="btn btn-outline-inverse btn-lg disabled" >Fork On Github</a>
            <a href="super/?a=router" target='_blank' class="btn btn-outline-inverse btn-lg" >route</a>
            <a href="super/home/params/123" target='_blank' class="btn btn-outline-inverse btn-lg" >params</a>
            <a href="super/home/rej" target='_blank' class="btn btn-outline-inverse btn-lg" >依赖注入</a>
            <a href="super/home/gh" target='_blank' class="btn btn-outline-inverse btn-lg" >规划</a>


                  </p>
    </div>
</main>
<?php View::tplInclude('Public/footer'); ?>
