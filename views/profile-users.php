<? use models\UserModel;

global $APPLICATION; ?>
<div class="container">
    <div class="row add-panel">
        <div class="col-md-6">
            Управление пользователями
        </div>
        <div class="col-md-6">
            <button type="button" name="ADD_USER_BTN" class="btn btn-primary add-user-btn" data-path="<?= self::getUrl('ajax', 'usercreate') ?>">Добавить пользователя</button>
        </div>
    </div>
    <? if (!empty($arUsers)) { ?>
        <? foreach ($arUsers as $usID => $userData) { ?>
            <div class="row user-item">
                <div class="col-md-2">
                    <a href="" class="update-user-link" data-id="<?= $userData['ID'] ?>" data-path="<?= self::getUrl('ajax', 'usercreate') ?>"><?= $userData['NAME'] ?></a>
                </div>
                <div class="col-md-2">
                    <?= UserModel::RANG_DETAIL[$userData['ROLE']] ?>
                </div>
                <div class="col-md-2">
                    <? if (!empty($userData['CITY_DETAIL'])) { ?>
                        <?= implode('<br>', array_column($userData['CITY_DETAIL'], 'NAME')) ?>
                    <? } ?>
                </div>
                <div class="col-md-2">
                    <?= $userData['EMAIL'] ?>
                </div>
                <div class="col-md-2">
                    <?= $userData['PHONE'] ?>
                </div>
                <div class="col-md-2">
                    <a href="<?= $APPLICATION->GetCurPage(); ?>" data-id="<?= $userData['ID'] ?>" class="delete-user">Удалить</a>
                </div>
            </div>
        <? } ?>
    <? } ?>
</div>