<?php

require "../config.php";
require "../inc/auth.php";

checkAuth(true, true);

$page = [];
$page['title'] = "Diablo 2 Item Store - Dashboard";

require "../theme/admin-header.php";

if(@$_POST['sa'] === "add-item") {
	$sql = "INSERT INTO d2_full_items SET fi_name = '" . mysqli_real_escape_string($mysqli, $_POST['item-name']) . "', fi_line = '" . mysqli_real_escape_string($mysqli, $_POST['item-line']) . "', 
		fi_img = '" . mysqli_real_escape_string($mysqli, $_POST['item-image']) . "', fi_type = '" . mysqli_real_escape_string($mysqli, $_POST['item-type']) . "', 
		fi_group = '" . mysqli_real_escape_string($mysqli, $_POST['item-group']) . "', fi_price_usd = '" . mysqli_real_escape_string($mysqli, $_POST['item-price']) . "', 
		fi_descript = '" . mysqli_real_escape_string($mysqli, str_replace("\n", "<br>", $_POST['item-desc'])) . "'";
	if($mysqli->query($sql)) {
		echo '<div class="row"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 centerAll colorWhite">Added new item.</div></div>';
	} else {
		echo '<div class="row"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 centerAll colorWhite">Error adding new item. [' . $mysqli->error . ']</div></div>';
	}
	
}

?>

<form method="POST" class="addItemBox">
	
	<input type="hidden" name="sa" value="add-item">
	
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
			<b>Item Name:</b>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
			<input type="text" name="item-name" value="<?php echo @$_POST['item-name']; ?>">
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
			<b>Simple Pickit Line:</b>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
			<input type="text" name="item-line" value="<?php echo @$_POST['item-line']; ?>">
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
			<b>Item Type:</b>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
			<select name="item-type">
				<option value="Runeword"<?php if(@$_POST['item-type'] === "Runeword") echo " selected"; ?>>Runeword</option>
				<option value="Crafted"<?php if(@$_POST['item-type'] === "Crafted") echo " selected"; ?>>Crafted</option>
				<option value="Unique"<?php if(@$_POST['item-type'] === "Unique") echo " selected"; ?>>Unique</option>
				<option value="Rare"<?php if(@$_POST['item-type'] === "Rare") echo " selected"; ?>>Rare</option>
				<option value="Set"<?php if(@$_POST['item-type'] === "Set") echo " selected"; ?>>Set</option>
				<option value="Magic"<?php if(@$_POST['item-type'] === "Magic") echo " selected"; ?>>Magic</option>
				<option value="Superior"<?php if(@$_POST['item-type'] === "Superior") echo " selected"; ?>>Superior</option>
				<option value="Normal"<?php if(@$_POST['item-type'] === "Normal") echo " selected"; ?>>Normal</option>
				<option value="Low Quality"<?php if(@$_POST['item-type'] === "Low Quality") echo " selected"; ?>>Low Quality</option>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
			<b>Item Group:</b>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
			<select name="item-group">
				<option value="Amulet"<?php if(@$_POST['item-group'] === "Amulet") echo " selected"; ?>>Amulet</option>
				<option value="Ring"<?php if(@$_POST['item-group'] === "Ring") echo " selected"; ?>>Ring</option>
				<option value="Rune"<?php if(@$_POST['item-group'] === "Rune") echo " selected"; ?>>Rune</option>
				<option value="Armor"<?php if(@$_POST['item-group'] === "Armor") echo " selected"; ?>>Armor</option>
				<option value="Belt"<?php if(@$_POST['item-group'] === "Belt") echo " selected"; ?>>Belt</option>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 centerAll">
			<img src="#" class="imagePreview">
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
			<b>Image:</b>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
			<select name="item-image" class="itemImage">
				<?php
				$images = glob("../images/items/*.png");
				foreach($images as $image)
				{
					$img = str_replace("../images/items/", "", $image);
					if(@$_POST['item-image'] === $img)
						echo '<option value="' . $img . '" selected>' . $img . '</option>';
					else
						echo '<option value="' . $img . '">' . $img . '</option>';
				}
				?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
			<b>Item Price:</b>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
			<input type="text" name="item-price" value="<?php echo @$_POST['item-price']; ?>">
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<b>Description:</b>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<textarea name="item-desc"><?php echo @$_POST['item-desc']; ?></textarea>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<input type="submit" class="btn" value="Add Item">
		</div>
	</div>
	
</form>

<script type="text/javascript">
	jQuery(function($) {
		$('.itemImage').change(function(){
			var selectedText = $(this).find("option:selected").text();
			$('.imagePreview').attr("src", "../images/items/" + selectedText);
			return false;
		});
	});
</script>

<?php

require "../theme/admin-footer.php";
