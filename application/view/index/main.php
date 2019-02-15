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
    <?php if (!empty($reflection)) { ?>
        <section class="mt-5">
            <code>
                <?php if ($reflection->getNamespaceName()) { ?>
                    <p>namespace <?= $reflection->getNamespaceName(); ?>;</p>
                <?php } ?>
                
                <p>interface <?= $reflection->getShortName() ?>Interface &#123;</p>
                
                <?php if ($reflection->getMethods()) { ?>
                    <?php foreach ($reflection->getMethods() as $method) { ?>
                        <?php if ($method->isPublic()) { ?>
                            <p class="ml-5">
                                <?= $method->isAbstract() ? 'abstract ' : null ?>
                                public function <?= $method->name ?>
                                
                                <!-- Аргументы -->
                                <?php if ($method->getParameters()) { ?>
                                    (
                                    <?php foreach ($method->getParameters() as $parament) { ?>
                                        <?= $parament->getType() ?: 'mixed' ?> &#36;<?= $parament->name ?>
                                    <?php } ?>
                                    ) :
                                <?php } else { ?>
                                    () :
                                <?php } ?>
                                    
                                <!-- Возвращаемое значение -->
                                <?php if ($method->getReturnType()) { ?>
                                    <?= $method->getReturnType() ?>;
                                <?php } else { ?>
                                    mixed;
                                <?php } ?>
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

