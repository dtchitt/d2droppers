			
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<footer>
						When purchasing you are buying the time and effort put into finding the item not the item itself. All items are owned by Blizzard and their respective owners.
					</footer>
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
			function mRound(num, places) {
				var multiplier = Math.pow(10, places); 
				return (Math.round(num * multiplier) / multiplier);
			}
			
			function setCookie(name,value,days) {
				var expires = "";
				if (days) {
					var date = new Date();
					date.setTime(date.getTime() + (days*24*60*60*1000));
					expires = "; expires=" + date.toUTCString();
				}
				document.cookie = name + "=" + (value || "")  + expires + "; path=/";
			}
			function getCookie(name) {
				var nameEQ = name + "=";
				var ca = document.cookie.split(';');
				for(var i=0;i < ca.length;i++) {
					var c = ca[i];
					while (c.charAt(0)==' ') c = c.substring(1,c.length);
					if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
				}
				return null;
			}
			function eraseCookie(name) {   
				document.cookie = name+'=; Max-Age=-99999999;';  
			}
			
			jQuery(function($) {
				//Total Price
				var ppi = "<?php echo $row['fi_price_usd']; ?>";
				$('.qtyCounter').on('input', function() {
					var total = mRound($('.qtyCounter').val() * ppi, 2);
					$('.totalPrice').html(total);
				});
				
				//eraseCookie("GSSTC005");
				var cartItems = [];
				var cartCookie = getCookie("GSSTC005");
				if(cartCookie) {
					cartItems = JSON.parse(cartCookie);
					var itemCount = 0;
					for (var i = 0; i < cartItems.length; i++) {
						itemCount = itemCount + cartItems[i][1];
					}
					$('.myCartLink').text("My Cart (" + itemCount + ") Items");
				}
				
				//Add item to cart
				$('.purchaseItemButton').on('click', function() {
					cartItems.push([$(this).data("id"), 1]);
					var itemCount = 0;
					for (var i = 0; i < cartItems.length; i++) {
						itemCount = itemCount + cartItems[i][1];
					}
					$('.myCartLink').text("My Cart (" + itemCount + ") Items");
					setCookie("GSSTC005", JSON.stringify(cartItems), 7);
					return false;
				});
				
				var fromURL = "<?php
				$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http') . '://' .  $_SERVER['HTTP_HOST'];
				echo $base_url . $_SERVER["REQUEST_URI"];
				?>";
				$('.ESCLEMenuBtn').on('click', function() {
					window.location.replace(fromURL + "?sr=east");
				});
				
				$('.WSCLEMenuBtn').on('click', function() {
					window.location.replace(fromURL + "?sr=west");
				});
				
			});
		</script>
	</body>
</html>