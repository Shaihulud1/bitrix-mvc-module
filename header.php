<? use models\UserModel;

$userModel = new UserModel;
global $APPLICATION;
if ($userModel->isAuth()) {
    $userData = $userModel->getUserById($userModel->getID());
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <? $APPLICATION->ShowCSS();
    ?>
    <? $APPLICATION->ShowHeadStrings();
    ?>
    <? $APPLICATION->ShowHeadScripts();
    ?>
    <style>
        .enter-form{
            width: 500px;
            position: absolute;
            top: 20vh;
            left: 50vh;
        }

        .top-head {
            padding: 5vh 10vh 20vh 3vh;
        }

        li {
            list-style-type: none;
        }

        .menu ul li {
            display: inline-block;
            margin-left: 5vh;
        }

        .form-search input {
            margin-left: 1vh;
        }

        .login-user {
            cursor: pointer;
            /* margin-left: 80vh; */
        }

        .login-user ul {
            margin: 0;
            padding: 0;
            position: absolute;
            display: none;
        }

        . .add-panel {
            margin-bottom: 5vh;
        }

        .vacancies-wrap {
            display: none;
        }

        .manager-item {
            margin: 2vh 0vh;
        }

        .manager-vacancies {
            cursor: pointer;
        }

        .user-item {
            margin-bottom: 4vh;
        }

        .add-panel {
            margin-bottom: 10vh;
        }
    </style>
</head>

<body>
    <? if ($userModel->isAuth()) { ?>
        <div class="container-fluid top-head">
            <div class="row">
                <div class="col-md-6">
                    <div class="menu">
                        <ul>
                            <li><a href="<?= self::getUrl('admin', 'vacancy') ?>">Вакансии</a></li>
                            <? if ($userModel->isRolesAccess([UserModel::REGIONAL_MANAGER])) { ?>
                                <li><a href="<?= self::getUrl('admin', 'templates') ?>">Шаблоны</a></li>
                            <? } ?>
                            <li><a href="<?= self::getUrl('admin', 'archive') ?>">Архив</a></li>
                            <li><a href="" class="choose-city" data-path="<?= self::getUrl('ajax', 'choosecity') ?>">Выбрать город</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <form class="form-search" action="<?= self::getUrl('admin', 'vacancy') ?>" method="GET">
                        <input type="text" name="search_hr" value="<?= $_GET['search_hr'] ? htmlspecialchars(trim($_GET['search_hr'])) : "" ?>" placeholder="Выбрать менеджера">
                    </form>
                </div>
                <div class="col-md-2">
                    <div class="login-user">
                        <span class="login-name"><?= $userData['fullname'] ?></span>
                        <ul>
                            <li><a href="<?= self::getUrl('profile', 'selfsettings') ?>">Личные данные</a></li>
                            <? if ($userModel->isRolesAccess([UserModel::REGIONAL_MANAGER])) { ?>
                                <li><a href="<?= self::getUrl('profile', 'users') ?>">Пользователи</a></li>
                            <? } ?>
                            <li><a href="<?= self::getUrl('profile', 'exit') ?>">Выход</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


    <? } ?>