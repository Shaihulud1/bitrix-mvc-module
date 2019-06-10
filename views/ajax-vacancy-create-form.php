<? global $APPLICATION; ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form class="<?= $formType == "update" ? "update" : "add" ?>-vacancy-form" action="<?= $APPLICATION->GetCurPage(); ?>" method="POST" data-id="<?= $updateFormData['VACANCIES'][0]['ID'] ?>">
                <div class="form-group">
                    <label for="">Название</label>
                    <input type="text" name="NAME" value="<?= $updateFormData['VACANCIES'][0]['NAME'] ?>">
                </div>
                <div class="form-group">
                    <label for="">Шаблон</label>
                    <select class="template-select" name="TEMPLATE" data-path="<?= self::getUrl('ajax', 'gettemplate') ?>">
                        <? if ($formType != 'update') { ?>
                            <option value="" selected></option>
                        <? } ?>
                        <? foreach ($arSelect['templates'] as $templateSelect) { ?>
                            <option value="<?= $templateSelect['id'] ?>" <?= $updateFormData['VACANCIES'][0]['TEMPLATE_ID'] == $templateSelect['id'] ? "selected" : "" ?>><?= $templateSelect['name'] ?></option>
                        <? } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Город</label>
                    <select class="" name="CITY">
                        <? foreach ($arSelect['cities'] as $citySelect) { ?>
                            <option value="<?= $citySelect['ID'] ?>" <?= in_array($citySelect['ID'], $updateFormData['VACANCIES'][0]['CITIES']) ? "selected" : "" ?>>
                                <?= $citySelect['NAME'] ?>
                            </option>
                        <? } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Ответственный</label>
                    <select class="" name="HR">
                        <? foreach ($arSelect['users'] as $userSelect) { ?>
                            <option value="<?= $userSelect['ID'] ?>" <?= $updateFormData['VACANCIES'][0]['HR_ID'] == $userSelect['ID'] ? "selected" : "" ?>><?= $userSelect['NAME'] ?></option>
                        <? } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Текст</label>
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:fileman.light_editor",
                        "",
                        array(
                            "CONTENT" => htmlspecialcharsBack($updateFormData['VACANCIES'][0]['DETAIL_TEXT']),
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