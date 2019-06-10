<? global $APPLICATION; ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form class="<?= $formType == "update" ? "update" : "add" ?>-template-form" action="<?= $APPLICATION->GetCurPage(); ?>" method="post" data-id="<?= $updateFormData['id'] ?>">
                <div class="form-group">
                    <label for="">Название</label>
                    <input type="text" name="NAME" value="<?= $updateFormData['name'] ?>">
                </div>
                <div class="form-group">
                    <label for="">Текст шаблона</label>
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:fileman.light_editor",
                        "",
                        array(
                            "CONTENT" => htmlspecialcharsBack($updateFormData['template_text']),
                            "INPUT_NAME" => "TEXT",
                            "INPUT_CLASS" => "",
                            "INPUT_ID" => "TEXT_TEMPLATE",
                            "WIDTH" => "100%",
                            "HEIGHT" => "300px",
                            "RESIZABLE" => "Y",
                            "AUTO_RESIZE" => "Y",
                            "VIDEO_ALLOW_VIDEO" => "N",
                            "USE_FILE_DIALOGS" => "N",
                            "JS_OBJ_NAME" => "VACANCY_EDIT",
                            "ID" => "",
                        )
                    ); ?>
                </div>
                <? if (!empty($errors)) { ?>
                    <p style="color:red"><?= $errors ?></p>
                <? } ?>
                <input type="hidden" name="actionType" value="<?= $formType ?>">
                <button type="submit" class="btn btn-primary" name="button"><?= $formType == "update" ? "Изменить" : "Добавить" ?></button>
            </form>
        </div>
    </div>
</div>
