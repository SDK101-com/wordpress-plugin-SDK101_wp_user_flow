<?php
// =================================================
// Custom new user notification template
// =================================================
defined( 'ABSPATH' ) || exit;
?>
<div style='font-family:"Open sans",sans-serif;background-color: #f2f2f2;padding:50px;'>
    <div style="max-width:800px;margin:0 auto;background:#fff;border:#333 3px solid;border-radius:5px;text-align:center;">
        <!--
        <div style="text-align:center;background:#333;padding:5px 5px 2px;">
            <?php
				if($logoURL){ ?>
                		<img src="<?php echo $logoURL; ?>" alt="<?php echo $blogname; ?>" style="height:45px;">
           <?php }else{ ?>
						<h3 style="color:#fff;"><?php echo $blogname; ?></h3>
			<?php }?>
        </div>
        -->
        <div style="padding:30px 50px;">
            <h1 style='color:#111;font-family:"Raleway",sans-serif;font-weight:700;margin-top:0;text-align:center;'>
            	Welcome to <?php echo $blogname; ?>
            </h1>
            <p style="margin-top:30px;">Dear Parent <?php echo $user_name; ?></p>
            <p>Assalamu’alaikum Warahmatullahi Wabarakatuh.</p>
            
            <!-- <p>Welcome to <?php echo $blogname;?></p> -->
            
            <p>You're only steps away from completing your account creation.</p>
            <p>Please reset your password using the button below.</p>
            
            <a href="<?php echo $resetPasswordURL; ?>" 
            	style="	display:inline-block;
                		background:#ea640f;
                        text-transform:uppercase;
                        text-decoration:none;
                        padding:15px 30px;
                        margin:20px auto 30px;
                        color:#fff;
                        font-weight:bold;
                        border:#ea640f 2px solid;
                        border-radius:3px;">
            	Reset Password
            </a>
            <br>
            <p>Jazakallah<br/>
            	<!-- <strong>Sincerely,</strong><br/> -->
            	<?php echo $blogname; ?> Team
            </p>
        </div>
    </div>
    <div style="max-width:800px;margin:30px auto 0;text-align:center;color:#bbb;font-size:80%;">
    © <?php echo date("Y")." ".$blogname; ?> – All Rights Reserved.
    </div>
</div>