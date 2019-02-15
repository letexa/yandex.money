<h1>Генератор интерфейсов</h1>

<?php if ($errors) { ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error) { ?>
            <p><?= $error ?></p>
        <?php } ?>
    </div>
<?php } ?>

<form action="/" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <input type="file" name="file" />
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

<?php if (isset($reflection)) { ?>
    <?php if ($reflection) { ?>
        <section class="mt-5">
            <code>
                <?php if ($reflection->getNamespaceName()) { ?>
                    <p>namespace <?= $reflection->getNamespaceName(); ?>;</p>
                <?php } ?>
                
                <p>
                    interface <?= $reflection->getName() ?>Interface
                    <?php if ($reflection->getInterfaces()) { ?>
                        extends
                        <?php $i = 1; foreach ($reflection->getInterfaces() as $interface => $val) { ?>
                            <?= $interface ?><?= $i < count($reflection->getInterfaces()) ? ',' : '&nbsp;' ?>
                            <?php $i++ ?>
                        <?php } ?>
                    <?php } ?>
                    &#123;
                </p>
                
                <?php if ($reflection->getMethods()) { ?>
                    <?php foreach ($reflection->getMethods() as $method) { ?>
                        <?php if ($method->isPublic()) { ?>
                            <p class="ml-5">
                                public function <?= $method->name ?>();
                                <?php print_r($method->getParameters()) ?>
                            </p>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>

                <p>&#125;</p>
            </code>
        </section>
    <?php } else { ?>
        <div class="alert alert-warning mt-5" role="alert">Класс не найден</div>
    <?php } ?>
<?php } ?>

