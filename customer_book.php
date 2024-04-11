<?php
session_start();
include('db_connect.php');
if(isset($_GET['id']) && !empty($_GET['id']) ){
	$qry = $conn->query("SELECT * FROM schedule_list where id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $val){
		$meta[$k] =  $val;
	}
$bus = $conn->query("SELECT * FROM bus where id = ".$meta['bus_id'])->fetch_array();
$from_location = $conn->query("SELECT id,Concat(terminal_name,', ',city,', ',state) as location FROM location where id =".$meta['from_location'])->fetch_array();
$to_location = $conn->query("SELECT id,Concat(terminal_name,', ',city,', ',state) as location FROM location where id =".$meta['to_location'])->fetch_array();
$count = $conn->query("SELECT SUM(qty) as sum from booked where schedule_id =".$meta['id'])->fetch_array()['sum'];
}
if(isset($_SESSION['login_id']) && isset($_GET['bid'])){
	$booked = $conn->query("SELECT * FROM booked where id=".$_GET['bid'])->fetch_array();
	foreach($booked as $k => $val){
		$bmeta[$k] =  $val;
	}
}

$_SESSION['isSeatAvailable'] = ($count <= $meta['availability']) ? true : false;
?>
<div class="container-fluid">
	<form id="manage_book">
		<div class="col-md-12">
			<p><b>Bus:</b> <?php echo $bus['bus_number'] . ' | '.$bus['name'] ?></p>
			<p><b>From:</b> <?php echo $from_location['location'] ?></p>
			<p><b>To:</b> <?php echo $to_location['location'] ?></p>
			<p><b>Departure Time</b>: <?php echo date('M d,Y h:i A',strtotime($meta['departure_time'])) ?></p>
			<p><b>Estimated Time of Arrival:</b> <?php echo date('M d,Y h:i A',strtotime($meta['eta'])) ?></p>
			<?php if(($count < $meta['availability']) || isset($_SESSION['login_id'])): ?>
			<input type="hidden" class="form-control" id="sid" name="sid" value='<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>' required="">
			<input type="hidden" class="form-control" id="sid" name="bid" value='<?php echo isset($_GET['bid']) ? $_GET['bid'] : '' ?>' required="">
			
			<div class="form-group mb-2">
				<label for="name" class="control-label">Name</label>
				<input type="text" class="form-control" id="name" name="name" value="<?php echo isset($bmeta['name']) ? $bmeta['name'] : '' ?>">
			</div>
			<div class="form-group mb-2">
				<label for="qty" class="control-label">No of Tickets</label>
				<input type="number" min="1"  max="30" class="form-control text-right" id="qty" name="qty" value="<?php echo isset($bmeta['qty']) ? $bmeta['qty'] : '' ?>">
			</div>
			<div class="form-group mb-2">
				<label for="qty" class="control-label">Email Address</label>
				<input type="email" class="form-control" id="email" name="email" value="<?php echo isset($bmeta['email']) ? $bmeta['email'] : '' ?>">
			</div>
			<div class="form-group mb-2">
				<label for="qty" class="control-label">Phone Number</label>
				<input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo isset($bmeta['phoneNumber']) ? $bmeta['phoneNumber'] : '' ?>">
			</div>
			<?php if(isset($_SESSION['login_id'])): ?>
			<div class="form-group mb-2">
				<label for="qty" class="control-label">Status</label>
				<select  class="form-control" id="status" name="status" value="<?php echo isset($bmeta['qty']) ? $bmeta['qty'] : '' ?>">
					<option value="1" <?php echo isset($bmeta['status']) && $bmeta['status'] == 1 ? "selected" : '' ?>>Paid</option>
					<option value="0" <?php echo isset($bmeta['status']) && $bmeta['status'] == 0 ? "selected" : '' ?>>Unpaid</option>
				</select>
			</div>
			<?php endif; ?>
			<?php else: ?>
			<h3>No Available seat</h3>
			<style>
				.uni_modal .modal-footer{
					display: none;
				}
				#book_now_button  {
					display: <?= $_SESSION['isSeatAvailable'] ? 'none !important' : 'block' ?>;
				}
			</style>
			<?php endif; ?>
		</div>
	</form>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
	function payWithPaystack(qty = 1, email = "", customerName = "", phoneNumeber = ""){
		var handler = PaystackPop.setup({
		key: 'pk_test_fe2372d9b927ed717661fbf612f9e0d1b72264a1',
		email: email,
		amount: '<?= $meta['price']?>' * qty * 100,
		ref: ''+Math.floor((Math.random() * 1000000000) + 1), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
		metadata: {
			custom_fields: [
				{
					display_name: customerName,
					variable_name: "mobile_number",
					value: phoneNumeber,
					qty
				}
			]
		},
		callback: function(response){
			console.log(JSON.stringify(response, null, 2))
			alert_toast('Payment successful, your receipt has been sent to your email','success');
			end_load()
			return response
		},
		onClose: function(){
			alert('window closed');
			end_load()
		}
		});
		handler.openIframe();
  	}
	$('#manage_book').submit(function(e){
		// console.log($(this).serializeArray());
		e.preventDefault()

		const obj = decodeStr($(this).serialize())

		const { bid, sid, ...rest } = obj
		const isFormValid = validateFormData({ ...rest })
		if (!isFormValid) {
			alert("Please fill all input fields")
			return;
		}
		console.log(obj)
		start_load()
		payWithPaystack(obj.qty, obj.email, obj.name, obj.phoneNumeber)

		$.ajax({
			url:'./book_now.php',
			method:'POST',
			data:$(this).serialize(),
			error: function (err) {
				console.log(err)
    			end_load()
    			alert_toast('An error occured','danger');
			},
			success: function(resp){
				resp = JSON.parse(resp)
				console.log(resp);
				if(resp.status == 1){
    				end_load()
    				$('.modal').modal('hide')
    				alert_toast('Data successfully saved','success');
    				if('<?php echo !isset($_SESSION['login_id']) ?>' == 1){
    				$('#book_modal .modal-body').html('<div class="text-center"><p><strong><h3>'+resp.ref+'</h3></strong></p><small>Reference Number</small><br/><small>Copy or Capture your Reference number </small></div>')
    				$('#book_modal').modal('show')
    				}else{
    					load_booked();
    				}
				}
			}
		})
	})
	$('.datetimepicker').datetimepicker({
	    format:'Y/m/d H:i',
	    startDate: '+3d'
	});

	function decodeStr(encodedUri) {
		let pairs = encodedUri.split('&');
		let obj = {};

		for (let i = 0; i < pairs.length; i++) {
			let pair = pairs[i].split('=');
			var decodedValue = decodeURIComponent(pair[1] || '');
			obj[pair[0]] = decodedValue === '' ? null : decodedValue;
		}

		return obj

	}

	function validateFormData(params = {}) {
		const values = Object.values(params)
		return values.every((item) => item !== null)
	}
</script>