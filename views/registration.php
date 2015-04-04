<?php

echo '
<div class="inner">
<!--
	<h1>' . __( 'Register' ) . '</h1>
-->

	<div class="notice">
		<p>
			Some random notice!
		</p>
	</div>

<!--
	' . $comicjet_login->login_form() . '
-->

	<form class="content" action="" method="post">

		<p>
			<label for="username">' . __( 'Username' ) . '</label>
			<input type="text" id="username" name="username" value="" />
		</p>
		<p>
			<label for="email">' . __( 'Email' ) . '</label>
			<input type="email" id="email" name="email" value="" />
		</p>
		<p>
			<label for="password">' . __( 'Password' ) . '</label>
			<input type="password" id="password" name="password" value="" />
		</p>
		<p>
			<input type="submit" value="Register" />
		</p>

	</form>
</div>';
