<?php
$data = [
    'title' => '规划',
    'body_class' => 'bs-docs-home',
];
View::tplInclude('Public/header', $data); ?>
<main class="bs-docs-masthead" id="content" role="main">
    <div class="container">
        <h1><?php echo $data['title'];?></h1>
        <p class="lead">单文件PHP框架，羽量级网站开发首选</p>
        <p>本项目由<a href='http://leo108.com' target='_blank'>leo108</a>开发，遵循MIT协议。</p>
        <p>
           <pre> db</pre>
            table
            ldb
            cache
            rcba
            debug
            input
            model
            user
            library
            wideg
            helper
      </p>

    </div>
</main>
<?php View::tplInclude('Public/footer'); ?>
