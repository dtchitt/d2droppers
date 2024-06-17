<?php

require "../config.php";
require "../inc/discord.php";
require "../inc/item-alias.php";
require "../inc/pickit.php";

$fi = findItems("[name] == breastplate && [quality] == unique && [flag] != ethereal");

print_r($fi);