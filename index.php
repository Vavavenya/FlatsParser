<link rel="stylesheet" href="style.css">

<a href='/download.php'>Обновить</a>

<form action="index.php" method="get">
	<p><input name="typeSort" type="radio" value="ASC" <?= $_GET['typeSort'] !== 'ASC'?:'checked'; ?> >По возрастанию</p>
	<p><input name="typeSort" type="radio" value="DESC" <?= $_GET['typeSort'] !== 'DESC'?:'checked'; ?> >По убыванию</p>
	<p><select size="2" name="fieldSort">
		<option value="cost" <?= $_GET['fieldSort'] !== 'cost'?:'selected'; ?>>По цене</option>
		<option value="date" <?= $_GET['fieldSort'] !== 'date'?:'selected'; ?>>По дате</option>
	</select></p>
	<input type="submit"/>
</form>

<?php
include 'dbConnection.php';
$amountRecords = count(getAllFlatsId($databaseLink));
echo sprintf('Всего записей [%d]', $amountRecords);
?>
<?php if ($amountRecords > 0): ?>
	<?
	$_GET['typeSort'] = $_GET['typeSort']?:'';
	$_GET['fieldSort'] = $_GET['fieldSort']?:'id';
	$sqlQuery = sprintf('SELECT * FROM ad_flats ORDER BY %s %s', $_GET['fieldSort'], $_GET['typeSort']);

	$result = mysqli_query($databaseLink, $sqlQuery);
	?>

	<?php while ($row = mysqli_fetch_array($result)):?>
		<?php $costLabel = ($row['cost'] == 0)?'Цена договорная':$row['cost']; ?>
		<hr>
		<div class='item'>
			<div class='head-item'>
				<h3><?=$row['title']?></h3>
				<?='#'.$row['id']?> || <?='Цена='.$costLabel ?> || <?='Дата='.$row['date']?>
			</div>
			<div class='body-item'>
				<?php if ($row['image_link'] != null): ?>
					<img src="<?=$row['image_link']?>">
				<?php endif; ?>
				<p class='text'><?=$row['description']?></p>
				<br>
				<a id='<?=$row['id']?>'
					onclick="showFullNumber(this.getAttribute('id'), this.getAttribute('data-full'))"
					data-full="<?=$row['phone_number']?>"
					style="cursor: pointer;">
					<?=mb_strimwidth($row['phone_number'], 0, 13, '...')?>

				</a>
			</div>
		</div>

		<?endwhile;?>
		<?else:?>
		Загрузите записи
	<?php endif; ?>

	<script language="JavaScript" type="text/javascript">

		function showFullNumber(elementId, fullNumber)
		{
			document.getElementById(elementId).textContent = fullNumber;
		}
	</script>