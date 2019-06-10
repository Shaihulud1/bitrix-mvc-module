<?
global $APPLICATION;
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form class="enter-form" action="<?= $APPLICATION->GetCurPage(); ?>" method="POST">
                <div class="form-group">
                    <label for="exampleInputEmail1">Email</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Введите email" name="USER_EMAIL">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Пароль</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Введите пароль" name="USER_PASS">
                </div>
                <? if (!empty($errors)) { ?>
                    <p style="color:red"><?= $errors ?></p>
                <? } ?>
                <button type="submit" class="btn btn-primary" name="SAVE_FORM">Войти</button>
            </form>
        </div>
    </div>
</div>