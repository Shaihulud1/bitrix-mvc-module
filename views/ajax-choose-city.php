<form class="choose-city-form" action="<?=$APPLICATION->GetCurPage();?>" method="post" style="text-align:center;">
    <div class="form-group">
        <select class="" name="" multiple>
            <?foreach($arSelect['cities'] as $city){?>
                <option value="<?=$city['ID']?>"><?=$city['NAME']?></option>
            <?}?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary" name="button">Выбрать</button>
</form>
