
<form action="<?echo $APPLICATION->GetCurPage();?>" METHOD="get">
	<?echo bitrix_sessid_post(); ?>
	<input type="hidden" name="lang" value="<?echo LANGUAGE_ID ?>">
	<input type="hidden" name="id" value="VitaVacancies">
	<input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="delete_tables" value="N">
	<p><input type="checkbox" name="delete_tables" id="save_tables" value="Y"><label for="save_tables">Удалить таблицы</label></p>
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL"); ?>">
</form>
