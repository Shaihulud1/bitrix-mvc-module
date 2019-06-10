<?global $APPLICATION;?>
<form class="choose-city-form" action="<?=$APPLICATION->GetCurPage();?>" method="post" style="text-align:center;">
    <div class="form-group">
        <select class="" name="CITY[]" multiple  size="10">
            <?foreach($arSelect['cities'] as $city){?>
                <option value="<?=$city['ID']?>" <?=in_array($city['ID'], $arSelect['choosenCities']) ? "selected" : ""?>>
                    <?=$city['NAME']?>
                </option>
            <?}?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary" name="button">Выбрать</button>
</form>
