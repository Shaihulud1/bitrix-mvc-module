<? global $APPLICATION; ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form class="<?= $formType == "update" ? "update" : "add" ?>-user-form" action="<?= $APPLICATION->GetCurPage(); ?>" method="post" data-id="<?= $updateFormData['id'] ?>">
                <div class="form-group">
                    <label for="">ФИО</label>
                    <input type="text" name="NAME" value="<?= $updateFormData['fullname'] ?>">
                </div>
                <div class="form-group">
                    <label for="">Телефон</label>
                    <input type="text" name="PHONE" value="<?= $updateFormData['phone'] ?>">
                </div>
                <div class="form-group">
                    <label for="">Email</label>
                    <input type="text" name="EMAIL" value="<?= $updateFormData['email'] ?>">
                </div>
                <div class="form-group">
                    <label for="">Город</label>
                    <select class="" name="CITY[]" multiple size="10">
                        <? foreach ($arSelect['cities'] as $citySelect) { ?>
                            <option value="<?= $citySelect['ID'] ?>" <?= in_array($citySelect['ID'], $updateFormData['city']) ? "selected" : "" ?>>
                                <?= $citySelect['NAME'] ?>
                            </option>
                        <? } ?>
                    </select>
                </div>
                <? if ($formType != "update") { ?>
                    <div class="form-group">
                        <label for="">Пароль</label>
                        <input type="password" name="PASSWRD" value="">
                    </div>
                    <div class="form-group">
                        <label for="">Повторите пароль</label>
                        <input type="password" name="PASSWRD_REPEAT" value="">
                    </div>
                <? } ?>
                <input type="hidden" name="actionType" value="<?= $formType ?>">
                <? if (!empty($errors)) { ?>
                    <p style="color:red"><?= $errors ?></p>
                <? } ?>
                <input type="hidden" name="actionType" value="<?= $formType ?>">
                <button type="submit" class="btn btn-primary" name="button"><?= $formType == "update" ? "Изменить" : "Добавить" ?></button>
            </form>
        </div>
    </div>
</div>