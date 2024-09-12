<?php session_start();?>
<!doctype html>
<html lang="en">
  <head>
  	<title>Contact Form 06</title>
    <meta charset="utf-8">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="css/styles.css">

	</head>
	<style>
		ul{
			margin-bottom: 0;
		}
		.cinema-heading {
    width: 100%; /* Make the image responsive to the container width */
    max-width: 150px; /* Set a maximum width for the image */
    height: auto; /* Maintain the aspect ratio of the image */
    border-radius: 15px; /* Rounded corners for a smoother look */
    display: block; /* Ensure image is a block element for alignment */
}

	</style>
	<body>
		<div class="mainContainer">
		<nav class="nav-bar">
		<img src="../images/Screenshot_2024-09-08_163828-removebg-preview.png" class="cinema-heading" alt="">
		<ul>
				<li><a href="../index.php">Home</a></li>
				<li><a href="../move.php">Movies</a></li>
				<li><a href="#">Contact us</a></li>
			</ul>
			<ul>
				<?php if (!empty($_SESSION['userName'])) { ?>
					<li><a href="../booking_success.php"><?php echo htmlspecialchars($_SESSION['userName']); ?></a></li>
					<li><a href="../ulogout.php">Logout</a></li>
				<?php } else { ?>
					<li><a href="../login.php">Login</a></li>
					<li><a href="../register.php">Register</a></li>
				<?php } ?>
			</ul>
		</nav>
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-12">
					<div class="wrapper">
						<div class="row no-gutters mb-5">
							<div class="col-md-7">
								<div class="contact-wrap w-100 p-md-5 p-4">
									<h3 class="mb-4">Contact Us</h3>
									<div id="form-message-warning" class="mb-4"></div> 
				      		<div id="form-message-success" class="mb-4">
				            Your message was sent, thank you!
				      		</div>
									<form method="POST" id="contactForm" name="contactForm" class="contactForm">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="label" for="name">Full Name</label>
													<input type="text" class="form-control" name="name" id="name" placeholder="Name">
												</div>
											</div>
											<div class="col-md-6"> 
												<div class="form-group">
													<label class="label" for="email">Email Address</label>
													<input type="email" class="form-control" name="email" id="email" placeholder="Email">
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label class="label" for="subject">Subject</label>
													<input type="text" class="form-control" name="subject" id="subject" placeholder="Subject">
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label class="label" for="#">Message</label>
													<textarea name="message" class="form-control" id="message" cols="30" rows="4" placeholder="Message"></textarea>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<input type="submit" value="Send Message" class="btn btn-primary">
													<div class="submitting"></div>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="col-md-5 d-flex align-items-stretch">
								<div id="map">
									<img src="../images/googlemap.PNG" style="width: 100%;height: 100%;object-fit:cover;" alt="image">
			          </div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="dbox w-100 text-center">
			        		<div class="icon d-flex align-items-center justify-content-center">
			        			<span class="fa fa-map-marker"></span>
			        		</div>
			        		<div class="text">
				            <p><span>Address:</span>Kanyoe, Taungoo</p>
				          </div>
			          </div>
							</div>
							<div class="col-md-3">
								<div class="dbox w-100 text-center">
			        		<div class="icon d-flex align-items-center justify-content-center">
			        			<span class="fa fa-phone"></span>
			        		</div>
			        		<div class="text">
				            <p><span>Phone:</span> <a href="tel://1234567920">09690428503</a></p>
				          </div>
			          </div>
							</div>
							<div class="col-md-3">
								<div class="dbox w-100 text-center">
			        		<div class="icon d-flex align-items-center justify-content-center">
			        			<span class="fa fa-paper-plane"></span>
			        		</div>
			        		<div class="text">
				            <p><span>Email:</span> <a href="mailto:info@yoursite.com">starlightcinema@gmail.com</a></p>
				          </div>
			          </div>
							</div>
							<div class="col-md-3">
								<div class="dbox w-100 text-center">
			        		<div class="icon d-flex align-items-center justify-content-center">
			        			<span class="fa fa-globe"></span>
			        		</div>
			        		<div class="text">
				            <p><span>Website</span> <a href="#">starlightcinema.com</a></p>
				          </div>
			          </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</section>
	<footer style="background-color: #1c2a48; padding: 20px 0; color: #ffffff; font-size: 14px;">
    <div style="width: 90%; max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
        <div style="display: flex; justify-content: space-between; width: 100%; margin-bottom: 20px;">
            <!-- Logo and Contact Section -->
            <div style="flex: 1; padding: 10px;">
                <div style="display: flex; align-items: center;">
                    <img src="../images/Screenshot_2024-09-08_163828-removebg-preview.png" alt="MMBusTicket Logo" style="height: 40px; margin-right: 10px;">
                    <h3 style="font-size: 18px; margin: 0;color:white;">StarLight Cinema</h3>
                </div>
                <div style="margin-top: 10px;">
                    <p style="margin: 5px 0;"><span style="margin-right: 5px;">&#9742;</span>09 690 428 503</p>
                    <p style="margin: 5px 0;"><span style="margin-right: 5px;">&#9993;</span>Ask a question</p>
                </div>
            </div>
            <!-- Information Section -->
            <div style="flex: 1; padding: 10px;">
                <h4 style="margin: 0 0 10px; font-size: 16px;color:white;">Menu</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin: 8px 0;"><a href="../index.php" style="color: #ffffff; text-decoration: none;padding-left: 0;">Home</a></li>
                    <li style="margin: 8px 0;"><a href="../move.php" style="color: #ffffff; text-decoration: none;padding-left: 0;">Movies</a></li>
                    <li style="margin: 8px 0;"><a href="#" style="color: #ffffff; text-decoration: none;padding-left: 0;">Contact us</a></li>
                </ul>
            </div>
            <!-- Legal Section -->
            <div style="flex: 1; padding: 10px;">
                <h4 style="margin: 0 0 10px; font-size: 16px;color:white;">Place</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin: 8px 0;"><a href="#" style="color: #ffffff; text-decoration: none;padding-left: 0;">Taungoo</a></li>
                    <li style="margin: 8px 0;"><a href="#" style="color: #ffffff; text-decoration: none;padding-left: 0;">Kanyoe</a></li>
                </ul>
            </div>
        </div>
        <!-- Footer Bottom -->
        <div style="text-align: center; border-top: 1px solid #0e1726; padding-top: 10px; width: 100%;">
            <p style="margin: 0;">&copy; 2024 starlightcinema.com</p>
        </div>
    </div>
</footer>

	</div>
	<script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.validate.min.js"></script>
  <script src="js/main.js"></script>

	</body>
</html>

