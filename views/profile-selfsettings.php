<? use models\UserModel;

global $APPLICATION;
?>
<? global $APPLICATION; ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form class="" action="<?= $APPLICATION->GetCurPage(); ?>" method="POST">
                <div class="form-group">
                    <label for="">ФИО</label>
                    <input type="text" name="NAME" value="<?= $selfUserData['fullname'] ?>">
                </div>
                <div class="form-group">
                    <label for="">Должность</label>
                    <span><?= UserModel::RANG_DETAIL[$selfUserData['role']] ?></span>
                </div>
                <div class="form-group">
                    <label for="">Город</label>
                    <? if ($isAdmin) { ?>
                        <select class="" name="CITY[]" multiple size="10">
                            <? foreach ($arSelect['cities'] as $city) { ?>
                                <option value="<?= $city['ID'] ?>" <?= in_array($city['ID'], $selfUserData['city']) ? "selected" : "" ?>>
                                    <?= $city['NAME'] ?>
                                </option>
                            <? } ?>
                        </select>
                    <? } else { ?>
                        <?= implode(', ', $arSelect['cities']); ?>
                    <? } ?>
                </div>
                <? if ($formType != "update") { ?>
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" name="EMAIL" value="<?= $selfUserData['email'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="">Телефон</label>
                        <input type="text" name="PHONE" value="<?= $selfUserData['phone'] ?>">
                    </div>
                <? } ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="button">Сохранить</button>
                </div>
                <? if (!empty($errors)) { ?>
                    <div class="form-group">
                        <p><?= $errors ?></p>
                    </div>
                <? } ?>
            </form>
        </div>
    </div>
</div>