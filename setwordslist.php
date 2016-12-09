<a href="index.php">Запустить парсер</a>
<form action="setwordslist.php" method="post">
    <p>Список слов для парсинга (через запятую):</p>
    <p><textarea name="wordslist" cols="60" rows="20"><?php if ($_POST['wordslist'] == null) echo file_get_contents('cfg/words_for_search.txt')?> </textarea></p>
    <p><input type="submit" value="Сохранить"/></p>
</form>

<?php
if ($_POST['wordslist'] != null) {
    file_put_contents('cfg/words_for_search.txt', htmlspecialchars($_POST['wordslist']));
    echo 'Данные сохранены';
}