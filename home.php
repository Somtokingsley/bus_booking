<section id="bg-bus" class="d-flex align-items-center">
    <div class="container">
      <?php if(!isset($_SESSION['login_id'])): ?>
      	<center><button class="btn btn-info btn-lg" type="button" id="book_now">Browse Schedule</button></center>
      <?php else: ?>
        <center><h1 style="color: #2c4964; font-size: 3rem;">Welcome</h1></center>
      <?php endif; ?>
    </div>
  </section>
  <script>
  	$('#book_now').click(function(){
      uni_modal('Find Schedule','book_filter.php')
  })
  </script>