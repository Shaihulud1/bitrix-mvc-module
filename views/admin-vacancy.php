<? global $APPLICATION; ?>
<div id="App" data-vacancies='<? /*=json_encode($arUsersVacancies)*/?>' class="wrapper">
    <div class="container">
        <div class="row add-panel">
            <div class="col-md-6">
                Менеджер
            </div>
            <div class="col-md-6">
                <button type="button" name="ADD_VACANCY_BTN" class="btn btn-primary add-vacancy-btn" data-path="<?= self::getUrl('ajax', 'vacancycreate') ?>">Добавить вакансию</button>
            </div>
        </div>
        <div class="row">
            <? foreach ($arUsersVacancies as $userData) { ?>
                <div class="col-md-12 manager-item">
                    <p class="manager-vacancies"><?= $userData['NAME'] ?></p>
                    <div class="vacancies-wrap">
                        <? foreach ($userData['VACANCIES'] as $vacancy) { ?>
                            <div class="vacancy-item">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-4"><a href="" class="update-vacancy-link" data-id="<?= $vacancy['ID'] ?>" data-path="<?= self::getUrl('ajax', 'vacancycreate') ?>"><?= $vacancy['NAME'] ?></a></div>
                                        <div class="col-md-4"><span><?= implode(', ', $vacancy['CITIES_DETAIL']) ?></span></div>
                                        <div class="col-md-4"><a href="" class="archive-vacancy" data-id="<?= $vacancy['ID'] ?>">В архив</a></div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>
            <? } ?>
            <? if ($pagination) {?>
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <? if ($pagination['PREV_BTN']) { ?>
                            <li class="page-item"><a class="page-link" href="#">Предыдущая</a></li>
                        <? } ?>
                        <? if ($pagination['PAGES_PREV']) {
                            foreach ($pagination['PAGES_PREV'] as $prevPage) { ?>
                                <li class="page-item"><a class="page-link" href="#"><?= $prevPage ?></a></li>
                            <? } ?>
                        <? } ?>
                        <li class="page-item active"><a class="page-link" href="#"><?= $pagination['CUR_PAGE'] ?></a></li>
                        <? if ($pagination['PAGES_NEXT']) {
                            foreach ($pagination['PAGES_NEXT'] as $nextPage) { ?>
                                <li class="page-item"><a class="page-link" href="#"><?= $nextPage ?></a></li>
                            <? } ?>
                        <? } ?>
                        <? if ($pagination['NEXT_BTN']) { ?>
                            <li class="page-item"><a class="page-link" href="#">Следующая</a></li>
                        <? } ?>
                        </ul>
                    </nav>
                <? } ?>
            </div>
        </div>
    </div>