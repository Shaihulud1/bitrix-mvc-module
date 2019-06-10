<? global $APPLICATION; ?>
<div class="wrapper">
    <div class="container">
        <div class="row add-panel">
            <div class="col-md-6">
                Шаблоны вакансий
            </div>
            <div class="col-md-6">
                <button type="button" name="ADD_TEMPLATE_BTN" class="btn btn-primary add-template-btn" data-path="<?= self::getUrl('ajax', 'templatecreate') ?>">Добавить шаблон</button>
            </div>
        </div>
        <div class="row">
            <? if (!empty($arTemplates)) { ?>
                <? foreach ($arTemplates as $template) { ?>
                    <div class="col-md-6">
                        <a href="" class="update-template-link" data-id="<?= $template['id'] ?>" data-path="<?= self::getUrl('ajax', 'templatecreate') ?>"><?= $template['name'] ?></a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= $APPLICATION->GetCurPage(); ?>" data-id="<?= $template['id'] ?>" class="delete-template">Удалить</a>
                    </div>
                <? } ?>
            <? } ?>
        </div>
    </div>
</div>
<? if (!empty($errors)) { ?>
    <div class="">
        <?= $errors ?>
    </div>
<? } ?>