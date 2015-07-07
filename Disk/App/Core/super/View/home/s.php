<?php
$data = [
    'title' => 'S对象',
    'body_class' => 'bs-docs-home',
];
View::tplInclude('Public/header', $data); ?>
<main class="bs-docs-masthead" id="content" role="main">
    <div class="container">
        <h1><?php echo $data['title'];?></h1>
        <p>
            <p class="lead">S对象在本系统中是个及其重要的对象</p>
        <p>S对象在控制中调用方法 <mall>$this->S</small> 其中内置的对象在Seter/Config/Default.php中进行配置</p>
        <p>在控制器中已经用依赖注入的方法对部分对象进行了重定向 这部分对象是 ：db table model request user 其中table、user依赖于db 单例调用 \Seter\Seter::getInstance();</p>

            <a href="/super/home/srequest" target='_blank' class="btn btn-outline-inverse btn-lg" >request</a>
            <a href="/super/home/gh" target='_blank' class="btn btn-outline-inverse btn-lg" >model</a>
        <br>
        <br>
            <a href="/super/home/gh" target='_blank' class="btn btn-outline-inverse btn-lg" >db</a>
->          <a href="/super/home/gh" target='_blank' class="btn btn-outline-inverse btn-lg" >table</a>
            <a href="/super/home/gh" target='_blank' class="btn btn-outline-inverse btn-lg" >user</a>

      </p>

    </div>
</main>
<?php View::tplInclude('Public/footer'); ?>
