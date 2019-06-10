<?global $APPLICATION;?>
<div id="App" data-vacancies='<?/*=json_encode($arUsersVacancies)*/?>' class="wrapper">
    <div class="container">
        <div class="row">
            <?foreach($arUsersVacancies as $userData){?>
                <div class="col-md-12 manager-item">
                    <p class="manager-vacancies"><?=$userData['NAME']?></p>
                    <div class="vacancies-wrap">
                        <?foreach($userData['VACANCIES'] as $vacancy){?>
                            <div class="vacancy-item">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-4"><?=$vacancy['NAME']?></div>
                                        <div class="col-md-4"><span><?=implode(', ', $vacancy['CITIES_DETAIL'])?></span></div>
                                        <div class="col-md-4"><a href="" class="unarchive-vacancy" data-id="<?=$vacancy['ID']?>">Убрать из архива</a></div>
                                    </div>
                                </div>
                            </div>
                        <?}?>
                    </div>
                </div>
            <?}?>
            <?/*<div class="col-md-12" v-for="user in vacancies">
                <p>{{user.NAME}}</p>
                <div class="vacancy-item" v-if="user.VACANCIES.length > 0" v-for="vacancy in user.VACANCIES">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-4">{{vacancy.NAME}}</div>
                            <div class="col-md-4"><span v-if="vacancy.CITIES_DETAIL.length > 0" v-for="city in vacancy.CITIES_DETAIL">{{city}}</span></div>
                            <div class="col-md-4"><a href="" @click="ajaxBackendAction(event, 'unarchive')" :data-id="vacancy.ID">В архив</a></div>
                        </div>
                    </div>
                </div>
            </div>*/?>
        </div>
    </div>
</div>
